<?php

declare(strict_types=1);

namespace Tests\Contract;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Tests\Mocks\MockPsrClient;
use VicidialApi\Exceptions\RetryException;
use VicidialApi\Exceptions\TransportException;
use VicidialApi\Transporters\HttpTransporter;
use VicidialApi\ValueObjects\RetryConfig;
use VicidialApi\ValueObjects\Transporter\BaseUri;
use VicidialApi\ValueObjects\Transporter\Headers;
use VicidialApi\ValueObjects\Transporter\Payload;
use VicidialApi\ValueObjects\Transporter\QueryParams;

/**
 * Retry decisions driven by the HTTP status code, for both client styles:
 * plain PSR-18 clients that return error responses, and Guzzle which throws
 * ClientException (4xx) / ServerException (5xx) carrying the response.
 */
final class RetryStatusCodeTest extends TestCase
{
    private static function transporter(MockPsrClient $client, RetryConfig $retryConfig): HttpTransporter
    {
        return new HttpTransporter(
            client: $client,
            baseUri: BaseUri::from('http://vicidial.test'),
            headers: Headers::create(),
            queryParams: QueryParams::create(),
            streamHandler: fn ($request) => $client->sendRequest($request),
            retryConfig: $retryConfig,
        );
    }

    private static function fastRetry(int $maxAttempts = 3, bool $retryOn5xx = true, bool $retryOn429 = true, bool $retryOnNetworkErrors = true): RetryConfig
    {
        return RetryConfig::custom(
            maxAttempts: $maxAttempts,
            initialDelayMs: 0,
            maxDelayMs: 0,
            retryOn5xx: $retryOn5xx,
            retryOnNetworkErrors: $retryOnNetworkErrors,
            retryOn429: $retryOn429,
        );
    }

    private static function text(int $status, string $body): Response
    {
        return new Response($status, ['Content-Type' => 'text/plain'], $body);
    }

    private static function serverException(int $status = 503): ServerException
    {
        return new ServerException("HTTP $status", new Request('GET', 'http://vicidial.test/agc/api.php'), self::text($status, 'Service Unavailable'));
    }

    private static function clientException(int $status = 404): ClientException
    {
        return new ClientException("HTTP $status", new Request('GET', 'http://vicidial.test/agc/api.php'), self::text($status, 'Not Found'));
    }

    public function test_psr18_error_status_without_exception_is_retried(): void
    {
        $client = new MockPsrClient([self::text(503, 'Service Unavailable'), self::text(200, 'SUCCESS: ok - 6666')]);

        $response = self::transporter($client, self::fastRetry())->requestResponse(Payload::list('test'));

        $this->assertCount(2, $client->getRequests());
        $this->assertTrue($response->ok());
        $this->assertSame(200, $response->statusCode());
    }

    public function test_psr18_error_status_is_returned_when_attempts_are_exhausted(): void
    {
        $client = new MockPsrClient([self::text(502, 'Bad Gateway'), self::text(502, 'Bad Gateway'), self::text(502, 'Bad Gateway')]);

        $response = self::transporter($client, self::fastRetry(maxAttempts: 2))->requestResponse(Payload::list('test'));

        $this->assertCount(2, $client->getRequests());
        $this->assertTrue($response->failed());
        $this->assertSame(502, $response->statusCode());
    }

    public function test_psr18_error_status_is_not_retried_when_5xx_retry_is_off(): void
    {
        $client = new MockPsrClient([self::text(500, 'boom'), self::text(200, 'SUCCESS: ok')]);

        $response = self::transporter($client, self::fastRetry(retryOn5xx: false))->requestResponse(Payload::list('test'));

        $this->assertCount(1, $client->getRequests());
        $this->assertSame(500, $response->statusCode());
    }

    public function test_psr18_429_is_retried(): void
    {
        $client = new MockPsrClient([self::text(429, 'slow down'), self::text(200, 'SUCCESS: ok')]);

        $response = self::transporter($client, self::fastRetry())->requestResponse(Payload::list('test'));

        $this->assertCount(2, $client->getRequests());
        $this->assertTrue($response->ok());
    }

    public function test_guzzle_server_exception_is_retried(): void
    {
        // Regression: ServerException (5xx) was never retried, only ClientException (4xx) was inspected.
        $client = new MockPsrClient([self::serverException(), self::text(200, 'SUCCESS: ok - 6666')]);

        $response = self::transporter($client, self::fastRetry())->requestResponse(Payload::list('test'));

        $this->assertCount(2, $client->getRequests());
        $this->assertTrue($response->ok());
    }

    public function test_guzzle_server_exception_exhausts_into_retry_exception(): void
    {
        $client = new MockPsrClient([self::serverException(), self::serverException(), self::serverException()]);

        try {
            self::transporter($client, self::fastRetry(maxAttempts: 3))->requestContent(Payload::list('test'));
            $this->fail('expected RetryException');
        } catch (RetryException $e) {
            $this->assertSame(3, $e->getAttemptsMade());
            $this->assertCount(3, $client->getRequests());
            $this->assertInstanceOf(TransportException::class, $e->getPrevious());
            $this->assertInstanceOf(ServerException::class, $e->getPrevious()->getPrevious());
        }
    }

    public function test_guzzle_server_exception_without_retry_is_a_transport_exception(): void
    {
        $client = new MockPsrClient([self::serverException()]);

        $this->expectException(TransportException::class);

        try {
            self::transporter($client, RetryConfig::disabled())->requestContent(Payload::list('test'));
        } finally {
            $this->assertCount(1, $client->getRequests());
        }
    }

    public function test_guzzle_4xx_is_not_retried(): void
    {
        $client = new MockPsrClient([self::clientException(404), self::text(200, 'SUCCESS: ok')]);

        $this->expectException(TransportException::class);

        try {
            self::transporter($client, self::fastRetry())->requestContent(Payload::list('test'));
        } finally {
            $this->assertCount(1, $client->getRequests());
        }
    }

    public function test_guzzle_429_is_retried_when_enabled(): void
    {
        $client = new MockPsrClient([self::clientException(429), self::text(200, 'SUCCESS: ok')]);

        $response = self::transporter($client, self::fastRetry())->requestResponse(Payload::list('test'));

        $this->assertCount(2, $client->getRequests());
        $this->assertTrue($response->ok());
    }

    public function test_network_error_is_retried_then_succeeds(): void
    {
        $connect = new ConnectException('Connection refused', new Request('GET', 'http://vicidial.test'));
        $client = new MockPsrClient([$connect, self::text(200, 'SUCCESS: ok')]);

        $response = self::transporter($client, self::fastRetry())->requestResponse(Payload::list('test'));

        $this->assertCount(2, $client->getRequests());
        $this->assertTrue($response->ok());
    }

    public function test_network_error_exhausts_into_retry_exception(): void
    {
        $connect = new ConnectException('Connection refused', new Request('GET', 'http://vicidial.test'));
        $client = new MockPsrClient([$connect, $connect, $connect]);

        try {
            self::transporter($client, self::fastRetry(maxAttempts: 2))->requestContent(Payload::list('test'));
            $this->fail('expected RetryException');
        } catch (RetryException $e) {
            $this->assertSame(2, $e->getAttemptsMade());
            $this->assertCount(2, $client->getRequests());
        }
    }

    public function test_network_error_without_retry_is_a_transport_exception(): void
    {
        $connect = new ConnectException('Connection refused', new Request('GET', 'http://vicidial.test'));
        $client = new MockPsrClient([$connect]);

        $this->expectException(TransportException::class);
        self::transporter($client, self::fastRetry(retryOnNetworkErrors: false))->requestContent(Payload::list('test'));
    }
}
