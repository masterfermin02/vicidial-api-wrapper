<?php

namespace Fakers;

use GuzzleHttp\Psr7\Response as GuzzleResponse;
use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\ResponseInterface;
use VicidialApi\Contracts\TransporterContract;
use VicidialApi\ValueObjects\Transporter\Payload;
use VicidialApi\ValueObjects\Transporter\Response;

class FakeHttpTransporter implements TransporterContract
{
    public function __construct(
        public readonly string|array $content = ''
    ) {
    }

    public function requestObject(Payload $payload): Response
    {
        return Response::from($this->content);
    }

    public function requestContent(Payload $payload): string
    {
        return $this->contentToString();
    }

    public function requestStream(Payload $payload): ResponseInterface
    {
        $stream = Utils::streamFor($this->contentToString());

        return new GuzzleResponse(200, [], $stream); // This is a fake response
    }

    private function contentToString(): string
    {
        return is_array($this->content) ? json_encode($this->content) : $this->content;
    }
}
