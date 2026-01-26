<?php

declare(strict_types=1);

namespace Tests\Contract;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Tests\Mocks\MockPsrClient;
use VicidialApi\Transporters\HttpTransporter;
use VicidialApi\ValueObjects\RetryConfig;
use VicidialApi\ValueObjects\Transporter\BaseUri;
use VicidialApi\ValueObjects\Transporter\Headers;
use VicidialApi\ValueObjects\Transporter\Payload;
use VicidialApi\ValueObjects\Transporter\QueryParams;

/**
 * Contract tests for basic HTTP transport behavior
 *
 * Note: The HttpTransporter's retry logic is triggered by ClientException
 * (Guzzle-specific) for HTTP error codes. Pure PSR-18 clients return responses
 * with status codes without throwing exceptions, so these tests verify the
 * basic transport behavior works correctly with various configurations.
 *
 * Full retry behavior with 5xx errors is tested in integration tests with
 * actual Guzzle client that throws ClientException for error status codes.
 */
final class RetryBehaviorTest extends TestCase
{
    public function test_succeeds_on_first_try_without_retry(): void
    {
        $mockClient = new MockPsrClient([
            new Response(200, ['Content-Type' => 'text/plain'], 'SUCCESS'),
        ]);

        $retryConfig = RetryConfig::default();

        $transporter = new HttpTransporter(
            client: $mockClient,
            baseUri: BaseUri::from('https://api.test.com'),
            headers: Headers::create(),
            queryParams: QueryParams::create(),
            streamHandler: fn ($request) => $mockClient->sendRequest($request),
            retryConfig: $retryConfig,
        );

        $payload = Payload::list('test');
        $response = $transporter->requestResponse($payload);

        $this->assertTrue($response->ok());
        $this->assertEquals(1, count($mockClient->getRequests()));
    }

    public function test_makes_single_request_when_retry_disabled(): void
    {
        $mockClient = new MockPsrClient([
            new Response(200, ['Content-Type' => 'text/plain'], 'SUCCESS: test'),
        ]);

        $transporter = new HttpTransporter(
            client: $mockClient,
            baseUri: BaseUri::from('https://api.test.com'),
            headers: Headers::create(),
            queryParams: QueryParams::create(),
            streamHandler: fn ($request) => $mockClient->sendRequest($request),
            retryConfig: RetryConfig::disabled(),
        );

        $payload = Payload::list('test');
        $response = $transporter->requestResponse($payload);

        $this->assertTrue($response->ok());
        $this->assertEquals(1, count($mockClient->getRequests()));
    }

    public function test_transporter_accepts_retry_config(): void
    {
        $mockClient = new MockPsrClient([
            new Response(200, ['Content-Type' => 'text/plain'], 'SUCCESS'),
        ]);

        $retryConfig = RetryConfig::custom(
            maxAttempts: 5,
            initialDelayMs: 100,
            retryOn5xx: true,
        );

        $transporter = new HttpTransporter(
            client: $mockClient,
            baseUri: BaseUri::from('https://api.test.com'),
            headers: Headers::create(),
            queryParams: QueryParams::create(),
            streamHandler: fn ($request) => $mockClient->sendRequest($request),
            retryConfig: $retryConfig,
        );

        $payload = Payload::list('test');
        $response = $transporter->requestResponse($payload);

        $this->assertTrue($response->ok());
        $this->assertInstanceOf(HttpTransporter::class, $transporter);
    }

    public function test_handles_error_response_without_exception(): void
    {
        // PSR-18 clients can return error status codes without throwing exceptions
        // The transporter should handle these gracefully
        $mockClient = new MockPsrClient([
            new Response(500, ['Content-Type' => 'text/plain'], 'ERROR: Server error'),
        ]);

        $transporter = new HttpTransporter(
            client: $mockClient,
            baseUri: BaseUri::from('https://api.test.com'),
            headers: Headers::create(),
            queryParams: QueryParams::create(),
            streamHandler: fn ($request) => $mockClient->sendRequest($request),
            retryConfig: RetryConfig::disabled(),
        );

        $payload = Payload::list('test');
        $response = $transporter->requestResponse($payload);

        // Response should be parsed correctly even with 500 status
        $this->assertFalse($response->ok());
        $this->assertTrue($response->failed());
        $this->assertEquals(1, count($mockClient->getRequests()));
    }

    public function test_handles_multiple_requests_in_sequence(): void
    {
        $mockClient = new MockPsrClient([
            new Response(200, ['Content-Type' => 'text/plain'], 'SUCCESS: first'),
            new Response(200, ['Content-Type' => 'text/plain'], 'SUCCESS: second'),
        ]);

        $transporter = new HttpTransporter(
            client: $mockClient,
            baseUri: BaseUri::from('https://api.test.com'),
            headers: Headers::create(),
            queryParams: QueryParams::create(),
            streamHandler: fn ($request) => $mockClient->sendRequest($request),
            retryConfig: RetryConfig::default(),
        );

        $payload1 = Payload::list('test1');
        $response1 = $transporter->requestResponse($payload1);

        $payload2 = Payload::list('test2');
        $response2 = $transporter->requestResponse($payload2);

        $this->assertTrue($response1->ok());
        $this->assertTrue($response2->ok());
        $this->assertEquals(2, count($mockClient->getRequests()));
    }
}
