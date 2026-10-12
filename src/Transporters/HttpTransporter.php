<?php

declare(strict_types=1);

namespace VicidialApi\Transporters;

use Closure;
use GuzzleHttp\Exception\BadResponseException;
use JsonException;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Client\NetworkExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use VicidialApi\Contracts\TransporterContract;
use VicidialApi\Enums\Transporter\ContentType;
use VicidialApi\Exceptions\RetryException;
use VicidialApi\Exceptions\TransportException;
use VicidialApi\Exceptions\VicidialException;
use VicidialApi\Logger\NullLogger;
use VicidialApi\Responses\VicidialResponse;
use VicidialApi\ValueObjects\RetryConfig;
use VicidialApi\ValueObjects\Transporter\BaseUri;
use VicidialApi\ValueObjects\Transporter\Headers;
use VicidialApi\ValueObjects\Transporter\Payload;
use VicidialApi\ValueObjects\Transporter\QueryParams;
use VicidialApi\ValueObjects\Transporter\Response;

final class HttpTransporter implements TransporterContract
{
    public function __construct(
        private readonly ClientInterface $client,
        private readonly BaseUri $baseUri,
        private readonly Headers $headers,
        private readonly QueryParams $queryParams,
        private readonly Closure $streamHandler,
        private readonly RetryConfig $retryConfig = new RetryConfig,
        private readonly LoggerInterface $logger = new NullLogger,
    ) {
        //
    }

    /**
     * {@inheritDoc}
     */
    public function requestObject(Payload $payload): Response
    {
        $request = $payload->toRequest($this->baseUri, $this->headers, $this->queryParams);

        // Log the request
        $this->logRequest($request);

        $response = $this->sendRequest(fn (): ResponseInterface => $this->client->sendRequest($request));

        $contents = $response->getBody()->getContents();

        if (str_contains($response->getHeaderLine('Content-Type'), ContentType::TEXT_PLAIN->value)) {
            return Response::from($contents);
        }

        $this->throwIfJsonError($response, $contents);

        try {
            /** @var array{error?: array{message: string, type: string, code: string}} $data */
            $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $jsonException) {
            throw new VicidialException($jsonException->getMessage(), 0, $jsonException);
        }

        return Response::from($data);
    }

    /**
     * {@inheritDoc}
     */
    public function requestContent(Payload $payload): string
    {
        $request = $payload->toRequest($this->baseUri, $this->headers, $this->queryParams);

        // Log the request
        $this->logRequest($request);

        $response = $this->sendRequest(fn (): ResponseInterface => $this->client->sendRequest($request));

        $contents = $response->getBody()->getContents();

        $this->throwIfJsonError($response, $contents);

        return $contents;
    }

    /**
     * Request and return a VicidialResponse DTO.
     */
    public function requestResponse(Payload $payload): VicidialResponse
    {
        $request = $payload->toRequest($this->baseUri, $this->headers, $this->queryParams);

        // Log the request
        $this->logRequest($request);

        $response = $this->sendRequest(fn (): ResponseInterface => $this->client->sendRequest($request));

        $contents = $response->getBody()->getContents();

        $this->throwIfJsonError($response, $contents);

        // Extract headers
        $headers = [];
        foreach ($response->getHeaders() as $name => $values) {
            $headers[$name] = $values[0] ?? '';
        }

        $header = $payload->parameter('header');
        $hasHeader = is_string($header) ? strtoupper($header) === 'YES' : null;

        return VicidialResponse::from($contents, $headers, $response->getStatusCode(), $hasHeader);
    }

    /**
     * {@inheritDoc}
     */
    public function requestStream(Payload $payload): ResponseInterface
    {
        $request = $payload->toRequest($this->baseUri, $this->headers, $this->queryParams);

        // Log the request
        $this->logRequest($request);

        $response = $this->sendRequest(fn () => ($this->streamHandler)($request));

        $this->throwIfJsonError($response, $response);

        return $response;
    }

    /**
     * Log the outgoing request with sanitized parameters.
     */
    private function logRequest(RequestInterface $request): void
    {
        $uri = $request->getUri();
        $queryString = $uri->getQuery();

        // Parse and sanitize query parameters
        parse_str($queryString, $params);
        $sanitizedParams = $this->sanitizeParams($params);

        $this->logger->debug('Vicidial API request initiated', [
            'method' => $request->getMethod(),
            'uri' => (string) $uri->withQuery(''), // URL without query string
            'params' => $sanitizedParams,
        ]);
    }

    /**
     * Sanitize sensitive parameters for logging.
     *
     * @param  array<int|string, mixed>  $params
     * @return array<int|string, mixed>
     */
    private function sanitizeParams(array $params): array
    {
        $sensitiveKeys = ['pass', 'password', 'api_key', 'auth', 'token', 'secret'];
        $sanitized = [];

        foreach ($params as $key => $value) {
            $lowerKey = strtolower((string) $key);
            $isSensitive = false;

            foreach ($sensitiveKeys as $sensitiveKey) {
                if (str_contains($lowerKey, $sensitiveKey)) {
                    $isSensitive = true;
                    break;
                }
            }

            $sanitized[$key] = $isSensitive ? '***REDACTED***' : $value;
        }

        return $sanitized;
    }

    /**
     * Send the request, retrying according to the RetryConfig.
     *
     * Retries happen on three signals: an HTTP error status returned without an
     * exception (plain PSR-18 clients), an HTTP error status delivered as an
     * exception carrying the response (Guzzle's ClientException for 4xx and
     * ServerException for 5xx), and network errors. When the last attempt still
     * fails with an exception a RetryException (attempts > 1) or TransportException
     * (single attempt) is thrown; when a non-throwing client keeps answering with an
     * error status the last response is returned so the caller can inspect it.
     */
    private function sendRequest(Closure $callable): ResponseInterface
    {
        $maxAttempts = $this->retryConfig->isEnabled() ? $this->retryConfig->getMaxAttempts() : 1;
        $attempt = 0;

        while (true) {
            $attempt++;
            $startTime = microtime(true);

            $this->logger->debug('Vicidial API request', [
                'attempt' => $attempt,
                'max_attempts' => $maxAttempts,
            ]);

            try {
                $response = $callable();
            } catch (ClientExceptionInterface $clientException) {
                $duration = round((microtime(true) - $startTime) * 1000, 2);

                // HTTP error status delivered as an exception (Guzzle: 4xx ClientException, 5xx ServerException)
                if ($clientException instanceof BadResponseException) {
                    $errorResponse = $clientException->getResponse();
                    $statusCode = $errorResponse->getStatusCode();

                    $this->logger->warning('Vicidial API request failed with HTTP error', [
                        'attempt' => $attempt,
                        'status_code' => $statusCode,
                        'duration_ms' => $duration,
                        'message' => $clientException->getMessage(),
                    ]);

                    if ($this->shouldRetryStatusCode($statusCode, $attempt, $maxAttempts)) {
                        $this->waitBeforeRetry($attempt, "HTTP $statusCode");

                        continue;
                    }

                    $this->throwIfJsonError($errorResponse, $errorResponse->getBody()->getContents());
                    $this->throwFinal($clientException, $attempt);
                }

                if ($clientException instanceof NetworkExceptionInterface) {
                    $this->logger->error('Vicidial API request failed with network error', [
                        'attempt' => $attempt,
                        'duration_ms' => $duration,
                        'error' => $clientException->getMessage(),
                    ]);

                    if ($this->retryConfig->isEnabled() && $this->retryConfig->shouldRetryOnNetworkErrors() && $attempt < $maxAttempts) {
                        $this->waitBeforeRetry($attempt, 'network error');

                        continue;
                    }

                    $this->throwFinal($clientException, $attempt);
                }

                $this->logger->error('Vicidial API request failed', [
                    'attempt' => $attempt,
                    'duration_ms' => $duration,
                    'error' => $clientException->getMessage(),
                ]);

                $this->throwFinal($clientException, $attempt);
            }

            $duration = round((microtime(true) - $startTime) * 1000, 2);
            $statusCode = $response->getStatusCode();

            // HTTP error status returned without an exception (plain PSR-18 clients)
            if ($this->shouldRetryStatusCode($statusCode, $attempt, $maxAttempts)) {
                $this->logger->warning('Vicidial API request failed with HTTP error', [
                    'attempt' => $attempt,
                    'status_code' => $statusCode,
                    'duration_ms' => $duration,
                ]);

                $this->waitBeforeRetry($attempt, "HTTP $statusCode");

                continue;
            }

            $this->logger->info($statusCode < 400 ? 'Vicidial API request successful' : 'Vicidial API request completed with HTTP error status', [
                'attempt' => $attempt,
                'status_code' => $statusCode,
                'duration_ms' => $duration,
            ]);

            return $response;
        }
    }

    private function waitBeforeRetry(int $attempt, string $reason): void
    {
        $delay = $this->retryConfig->calculateDelay($attempt);

        $this->logger->info('Retrying Vicidial API request', [
            'attempt' => $attempt,
            'next_attempt' => $attempt + 1,
            'delay_ms' => $delay,
            'reason' => $reason,
        ]);

        if ($delay > 0) {
            usleep($delay * 1000);
        }
    }

    /**
     * Give up: RetryException when retries were made, TransportException otherwise.
     */
    private function throwFinal(ClientExceptionInterface $clientException, int $attempt): never
    {
        if ($attempt > 1) {
            $this->logger->error('Vicidial API request failed after all retries', [
                'attempts' => $attempt,
                'error' => $clientException->getMessage(),
            ]);

            throw new RetryException(
                sprintf('Request failed after %d attempts: %s', $attempt, $clientException->getMessage()),
                $attempt,
                0,
                new TransportException($clientException)
            );
        }

        throw new TransportException($clientException);
    }

    /**
     * Determine if we should retry based on HTTP status code.
     */
    private function shouldRetryStatusCode(int $statusCode, int $attempt, int $maxAttempts): bool
    {
        if (! $this->retryConfig->isEnabled() || $attempt >= $maxAttempts) {
            return false;
        }

        // Retry on 429 (Too Many Requests) if enabled
        if ($statusCode === 429 && $this->retryConfig->shouldRetryOn429()) {
            return true;
        }

        // Retry on 5xx (Server Errors) if enabled
        return $statusCode >= 500 && $statusCode < 600 && $this->retryConfig->shouldRetryOn5xx();
    }

    private function throwIfJsonError(ResponseInterface $response, string|ResponseInterface $contents): void
    {
        if ($response->getStatusCode() < 400) {
            return;
        }

        if (! str_contains($response->getHeaderLine('Content-Type'), ContentType::JSON->value)) {
            return;
        }

        if ($contents instanceof ResponseInterface) {
            $contents = $contents->getBody()->getContents();
        }

        try {
            /** @var array{error?: array{message: string|array<int, string>, type: ?string, code: string|int|null}} $response */
            $response = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);

            if (isset($response['error'])) {
                $message = $response['error']['message'];
                if (is_array($message)) {
                    $message = implode("\n", $message);
                }

                throw new VicidialException(
                    $message,
                    (int) ($response['error']['code'] ?? 0)
                );
            }
        } catch (JsonException $jsonException) {
            throw new VicidialException($jsonException->getMessage(), 0, $jsonException);
        }
    }
}
