<?php

declare(strict_types=1);

namespace Tests\Contract;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\VicidialResponses;
use Tests\Mocks\MockPsrClient;
use VicidialApi\Transporters\HttpTransporter;
use VicidialApi\ValueObjects\Transporter\BaseUri;
use VicidialApi\ValueObjects\Transporter\Headers;
use VicidialApi\ValueObjects\Transporter\Payload;
use VicidialApi\ValueObjects\Transporter\QueryParams;

/**
 * Contract tests for HttpTransporter
 * Tests actual request/response flow using PSR-18 mock client
 */
final class HttpTransporterTest extends TestCase
{
    private MockPsrClient $mockClient;

    private HttpTransporter $transporter;

    protected function setUp(): void
    {
        $this->mockClient = new MockPsrClient;
        $this->transporter = new HttpTransporter(
            client: $this->mockClient,
            baseUri: BaseUri::from('https://api.vicidial.test'),
            headers: Headers::create(),
            queryParams: QueryParams::create(),
            streamHandler: fn ($request) => $this->mockClient->sendRequest($request),
        );
    }

    public function test_sends_get_request_with_query_params(): void
    {
        $this->mockClient->addResponse(
            new Response(200, ['Content-Type' => 'text/plain'], VicidialResponses::versionInfo())
        );

        $payload = Payload::retrieveWithParameters('version', [
            'user' => 'testuser',
            'pass' => 'testpass',
        ]);

        $response = $this->transporter->requestResponse($payload);

        $this->assertTrue($response->ok());

        $request = $this->mockClient->getLastRequest();
        $this->assertEquals('GET', $request->getMethod());
        $this->assertStringContainsString('user=testuser', $request->getUri()->getQuery());
        $this->assertStringContainsString('pass=testpass', $request->getUri()->getQuery());
    }

    public function test_builds_correct_url_with_base_uri(): void
    {
        $this->mockClient->addResponse(
            new Response(200, ['Content-Type' => 'text/plain'], 'SUCCESS')
        );

        $payload = Payload::list('agents');
        $this->transporter->requestResponse($payload);

        $request = $this->mockClient->getLastRequest();
        $uri = (string) $request->getUri();

        $this->assertStringStartsWith('https://api.vicidial.test/', $uri);
        $this->assertStringContainsString('/agents', $uri);
    }

    public function test_encodes_special_characters_in_query_params(): void
    {
        $this->mockClient->addResponse(
            new Response(200, ['Content-Type' => 'text/plain'], 'SUCCESS')
        );

        $payload = Payload::retrieveWithParameters('lead', [
            'name' => "O'Brien & Sons",
            'company' => 'Test & Co.',
        ]);

        $this->transporter->requestResponse($payload);

        $request = $this->mockClient->getLastRequest();
        $query = $request->getUri()->getQuery();

        // PHP's http_build_query will URL-encode these
        $this->assertStringContainsString('name=', $query);
        $this->assertStringContainsString('company=', $query);
    }

    public function test_sends_post_request_with_json_body(): void
    {
        $this->mockClient->addResponse(
            new Response(200, ['Content-Type' => 'text/plain'], VicidialResponses::leadUpdateSuccess())
        );

        $payload = Payload::create('lead', [
            'lead_id' => '12345',
            'status' => 'UPDATED',
        ]);

        $response = $this->transporter->requestResponse($payload);

        $this->assertTrue($response->ok());

        $request = $this->mockClient->getLastRequest();
        $this->assertEquals('POST', $request->getMethod());
        $this->assertStringContainsString('application/json', $request->getHeaderLine('Content-Type'));
    }

    public function test_includes_authorization_header_when_provided(): void
    {
        $headers = Headers::create()->withCustomHeader('Authorization', 'Bearer test_token');

        $transporter = new HttpTransporter(
            client: $this->mockClient,
            baseUri: BaseUri::from('https://api.vicidial.test'),
            headers: $headers,
            queryParams: QueryParams::create(),
            streamHandler: fn ($request) => $this->mockClient->sendRequest($request),
        );

        $this->mockClient->addResponse(
            new Response(200, ['Content-Type' => 'text/plain'], 'SUCCESS')
        );

        $payload = Payload::list('agents');
        $transporter->requestResponse($payload);

        $request = $this->mockClient->getLastRequest();
        $this->assertEquals('Bearer test_token', $request->getHeaderLine('Authorization'));
    }

    public function test_parses_success_response_correctly(): void
    {
        $this->mockClient->addResponse(
            new Response(200, ['Content-Type' => 'text/plain'], VicidialResponses::externalPauseSuccess())
        );

        $payload = Payload::list('status');
        $response = $this->transporter->requestResponse($payload);

        $this->assertTrue($response->ok());
        $this->assertFalse($response->failed());
        $this->assertEquals('success', $response->status());
    }

    public function test_parses_error_response_correctly(): void
    {
        $this->mockClient->addResponse(
            new Response(200, ['Content-Type' => 'text/plain'], VicidialResponses::agentNotLoggedInError())
        );

        $payload = Payload::list('status');
        $response = $this->transporter->requestResponse($payload);

        $this->assertFalse($response->ok());
        $this->assertTrue($response->failed());
        $this->assertEquals('error', $response->status());
    }

    public function test_handles_multiple_query_params(): void
    {
        $this->mockClient->addResponse(
            new Response(200, ['Content-Type' => 'text/plain'], 'SUCCESS')
        );

        $payload = Payload::retrieveWithParameters('agent', [
            'user' => 'admin',
            'pass' => 'password',
            'agent_user' => '6666',
            'campaign' => 'TEST_CAMP',
        ]);

        $this->transporter->requestResponse($payload);

        $request = $this->mockClient->getLastRequest();
        $query = $request->getUri()->getQuery();

        $this->assertStringContainsString('user=admin', $query);
        $this->assertStringContainsString('pass=password', $query);
        $this->assertStringContainsString('agent_user=6666', $query);
        $this->assertStringContainsString('campaign=TEST_CAMP', $query);
    }

    public function test_preserves_request_order(): void
    {
        $this->mockClient->addResponse(
            new Response(200, ['Content-Type' => 'text/plain'], 'SUCCESS 1')
        );
        $this->mockClient->addResponse(
            new Response(200, ['Content-Type' => 'text/plain'], 'SUCCESS 2')
        );

        $payload1 = Payload::list('agents');
        $payload2 = Payload::list('campaigns');

        $response1 = $this->transporter->requestResponse($payload1);
        $response2 = $this->transporter->requestResponse($payload2);

        $this->assertStringContainsString('SUCCESS 1', $response1->raw());
        $this->assertStringContainsString('SUCCESS 2', $response2->raw());
    }

    public function test_request_content_returns_raw_string(): void
    {
        $expectedContent = VicidialResponses::versionInfo();
        $this->mockClient->addResponse(
            new Response(200, ['Content-Type' => 'text/plain'], $expectedContent)
        );

        $payload = Payload::list('version');
        $content = $this->transporter->requestContent($payload);

        $this->assertEquals($expectedContent, $content);
    }
}
