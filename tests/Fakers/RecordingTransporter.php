<?php

declare(strict_types=1);

namespace Tests\Fakers;

use GuzzleHttp\Psr7\Response as GuzzleResponse;
use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use VicidialApi\Contracts\TransporterContract;
use VicidialApi\Responses\VicidialResponse;
use VicidialApi\ValueObjects\Transporter\BaseUri;
use VicidialApi\ValueObjects\Transporter\Headers;
use VicidialApi\ValueObjects\Transporter\Payload;
use VicidialApi\ValueObjects\Transporter\QueryParams;
use VicidialApi\ValueObjects\Transporter\Response;

/**
 * Transporter fake that records the PSR-7 request each payload would produce,
 * so tests can assert on the final URL and query string.
 */
final class RecordingTransporter implements TransporterContract
{
    public ?RequestInterface $lastRequest = null;

    public function __construct(
        private readonly string $content = 'SUCCESS: ok - 6666',
        private readonly string $baseUri = 'http://vicidial.test',
    ) {}

    public function requestObject(Payload $payload): Response
    {
        $this->record($payload);

        return Response::from($this->content);
    }

    public function requestContent(Payload $payload): string
    {
        $this->record($payload);

        return $this->content;
    }

    public function requestResponse(Payload $payload): VicidialResponse
    {
        $this->record($payload);

        return VicidialResponse::from($this->content);
    }

    public function requestStream(Payload $payload): ResponseInterface
    {
        $this->record($payload);

        return new GuzzleResponse(200, [], Utils::streamFor($this->content));
    }

    /**
     * @return array<string, string>
     */
    public function query(): array
    {
        parse_str((string) $this->lastRequest?->getUri()->getQuery(), $query);

        /** @var array<string, string> $query */
        return $query;
    }

    public function rawQuery(): string
    {
        return (string) $this->lastRequest?->getUri()->getQuery();
    }

    public function uri(): string
    {
        return (string) $this->lastRequest?->getUri();
    }

    private function record(Payload $payload): void
    {
        $this->lastRequest = $payload->toRequest(
            BaseUri::from($this->baseUri),
            Headers::create(),
            QueryParams::create()->withParam('source', 'test')->withParam('user', 'u')->withParam('pass', 'p'),
        );
    }
}
