<?php

declare(strict_types=1);

namespace Tests\Unit\ValueObjects;

use PHPUnit\Framework\TestCase;
use VicidialApi\ValueObjects\Transporter\BaseUri;
use VicidialApi\ValueObjects\Transporter\Headers;
use VicidialApi\ValueObjects\Transporter\Payload;
use VicidialApi\ValueObjects\Transporter\QueryParams;

/**
 * Unit tests for Payload value object
 * Tests URL building and request construction
 */
final class PayloadTest extends TestCase
{
    private BaseUri $baseUri;

    private Headers $headers;

    private QueryParams $queryParams;

    protected function setUp(): void
    {
        $this->baseUri = BaseUri::from('https://api.test.com');
        $this->headers = Headers::create();
        $this->queryParams = QueryParams::create();
    }

    public function test_creates_list_payload(): void
    {
        $payload = Payload::list('agents');
        $request = $payload->toRequest($this->baseUri, $this->headers, $this->queryParams);

        $this->assertEquals('GET', $request->getMethod());
        $this->assertStringContainsString('/agents', (string) $request->getUri());
    }

    public function test_creates_retrieve_payload(): void
    {
        $payload = Payload::retrieve('agent', '6666');
        $request = $payload->toRequest($this->baseUri, $this->headers, $this->queryParams);

        $this->assertEquals('GET', $request->getMethod());
        $this->assertStringContainsString('/agent/6666', (string) $request->getUri());
    }

    public function test_creates_create_payload(): void
    {
        $payload = Payload::create('lead', [
            'phone_number' => '5551234567',
            'campaign' => 'TEST',
        ]);
        $request = $payload->toRequest($this->baseUri, $this->headers, $this->queryParams);

        $this->assertEquals('POST', $request->getMethod());
        $this->assertStringContainsString('/lead', (string) $request->getUri());
    }

    public function test_creates_delete_payload(): void
    {
        $payload = Payload::delete('lead', '12345');
        $request = $payload->toRequest($this->baseUri, $this->headers, $this->queryParams);

        $this->assertEquals('DELETE', $request->getMethod());
        $this->assertStringContainsString('/lead/12345', (string) $request->getUri());
    }

    public function test_get_payload_with_parameters_adds_query_string(): void
    {
        $payload = Payload::retrieveWithParameters('status', [
            'user' => 'admin',
            'agent' => '6666',
        ]);
        $request = $payload->toRequest($this->baseUri, $this->headers, $this->queryParams);

        $uri = (string) $request->getUri();
        $this->assertStringContainsString('user=admin', $uri);
        $this->assertStringContainsString('agent=6666', $uri);
    }

    public function test_post_payload_includes_json_body(): void
    {
        $payload = Payload::create('lead', [
            'lead_id' => '12345',
            'status' => 'UPDATED',
        ]);
        $request = $payload->toRequest($this->baseUri, $this->headers, $this->queryParams);

        $body = (string) $request->getBody();
        $decoded = json_decode($body, true);

        $this->assertEquals('12345', $decoded['lead_id']);
        $this->assertEquals('UPDATED', $decoded['status']);
    }

    public function test_payload_merges_with_existing_query_params(): void
    {
        $existingParams = QueryParams::create()
            ->withParam('user', 'admin')
            ->withParam('pass', 'secret');

        $payload = Payload::retrieveWithParameters('agent', [
            'agent_user' => '6666',
        ]);
        $request = $payload->toRequest($this->baseUri, $this->headers, $existingParams);

        $uri = (string) $request->getUri();
        $this->assertStringContainsString('user=admin', $uri);
        $this->assertStringContainsString('pass=secret', $uri);
        $this->assertStringContainsString('agent_user=6666', $uri);
    }

    public function test_payload_sets_content_type_header(): void
    {
        $payload = Payload::create('lead', ['test' => 'data']);
        $request = $payload->toRequest($this->baseUri, $this->headers, $this->queryParams);

        $contentType = $request->getHeaderLine('Content-Type');
        $this->assertEquals('application/json', $contentType);
    }

    public function test_constructs_full_url_correctly(): void
    {
        $payload = Payload::list('campaigns');
        $request = $payload->toRequest($this->baseUri, $this->headers, $this->queryParams);

        $uri = (string) $request->getUri();
        $this->assertEquals('https://api.test.com/campaigns', $uri);
    }

    public function test_handles_retrieve_with_suffix(): void
    {
        $payload = Payload::retrieve('campaign', 'TEST_CAMP', '/leads');
        $request = $payload->toRequest($this->baseUri, $this->headers, $this->queryParams);

        $uri = (string) $request->getUri();
        $this->assertStringContainsString('/campaign/TEST_CAMP/leads', $uri);
    }

    public function test_url_encodes_parameters_in_query_string(): void
    {
        $payload = Payload::retrieveWithParameters('lead', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);
        $request = $payload->toRequest($this->baseUri, $this->headers, $this->queryParams);

        $query = $request->getUri()->getQuery();

        // Spaces should be encoded
        $this->assertStringContainsString('John', $query);
        $this->assertStringContainsString('Doe', $query);

        // @ should be encoded
        $this->assertStringContainsString('example.com', $query);
    }

    public function test_handles_integer_parameter_values(): void
    {
        $payload = Payload::retrieveWithParameters('lead', [
            'lead_id' => 12345,
            'campaign_id' => 67890,
        ]);
        $request = $payload->toRequest($this->baseUri, $this->headers, $this->queryParams);

        $query = $request->getUri()->getQuery();
        $this->assertStringContainsString('lead_id=12345', $query);
        $this->assertStringContainsString('campaign_id=67890', $query);
    }

    public function test_handles_array_parameter_values(): void
    {
        $payload = Payload::retrieveWithParameters('campaigns', [
            'statuses' => ['ACTIVE', 'PAUSED'],
        ]);
        $request = $payload->toRequest($this->baseUri, $this->headers, $this->queryParams);

        $query = $request->getUri()->getQuery();
        // Arrays should be converted to comma-separated strings
        $this->assertStringContainsString('ACTIVE', $query);
        $this->assertStringContainsString('PAUSED', $query);
    }

    public function test_cancel_payload_method(): void
    {
        $payload = Payload::cancel('call', '1234567890');
        $request = $payload->toRequest($this->baseUri, $this->headers, $this->queryParams);

        $this->assertEquals('POST', $request->getMethod());
        $this->assertStringContainsString('/call/1234567890/cancel', (string) $request->getUri());
    }
}
