<?php

declare(strict_types=1);

namespace Tests\Mocks;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Mock PSR-18 HTTP Client for testing
 */
final class MockPsrClient implements ClientInterface
{
    /** @var array<RequestInterface> */
    private array $requests = [];

    /**
     * @param  array<ResponseInterface>  $responses
     */
    public function __construct(
        private array $responses = [],
        private ?\Throwable $exception = null,
    ) {}

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $this->requests[] = $request;

        if ($this->exception !== null) {
            throw $this->exception;
        }

        if (empty($this->responses)) {
            return new Response(200, ['Content-Type' => 'text/plain'], 'SUCCESS');
        }

        return array_shift($this->responses);
    }

    public function addResponse(ResponseInterface $response): self
    {
        $this->responses[] = $response;

        return $this;
    }

    /**
     * @return array<RequestInterface>
     */
    public function getRequests(): array
    {
        return $this->requests;
    }

    public function getLastRequest(): ?RequestInterface
    {
        return empty($this->requests) ? null : end($this->requests);
    }

    public function reset(): self
    {
        $this->requests = [];
        $this->responses = [];
        $this->exception = null;

        return $this;
    }
}
