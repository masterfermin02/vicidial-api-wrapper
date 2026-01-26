<?php

declare(strict_types=1);

namespace Tests\Unit\ValueObjects;

use PHPUnit\Framework\TestCase;
use VicidialApi\ValueObjects\ApiKey;
use VicidialApi\ValueObjects\BasicAuth;
use VicidialApi\ValueObjects\Transporter\Headers;

/**
 * Unit tests for Headers value object
 * Tests auth header logic (basic vs bearer)
 */
final class HeadersTest extends TestCase
{
    public function test_creates_empty_headers(): void
    {
        $headers = Headers::create();

        $this->assertEquals([], $headers->toArray());
    }

    public function test_creates_bearer_authorization_header(): void
    {
        $apiKey = ApiKey::from('test_api_key_12345');
        $headers = Headers::withAuthorization($apiKey);

        $expected = [
            'Authorization' => 'Bearer test_api_key_12345',
        ];

        $this->assertEquals($expected, $headers->toArray());
    }

    public function test_creates_basic_authorization_header(): void
    {
        $basicAuth = BasicAuth::from('admin', 'password123');
        $headers = Headers::withBasicAuthorization($basicAuth);

        // Basic auth should be base64 encoded
        $expectedEncoded = base64_encode('admin:password123');
        $expected = [
            'Authorization' => "Basic {$expectedEncoded}",
        ];

        $this->assertEquals($expected, $headers->toArray());
    }

    public function test_basic_auth_encoding_is_correct(): void
    {
        $basicAuth = BasicAuth::from('testuser', 'testpass');

        $encoded = $basicAuth->encode();
        $decoded = base64_decode($encoded);

        $this->assertEquals('testuser:testpass', $decoded);
    }

    public function test_basic_auth_with_special_characters(): void
    {
        $basicAuth = BasicAuth::from('user@example.com', 'p@ss:w0rd!');

        $encoded = $basicAuth->encode();
        $decoded = base64_decode($encoded);

        $this->assertEquals('user@example.com:p@ss:w0rd!', $decoded);
    }

    public function test_adds_content_type_header(): void
    {
        $headers = Headers::create()
            ->withContentType(\VicidialApi\Enums\Transporter\ContentType::JSON);

        $expected = [
            'Content-Type' => 'application/json',
        ];

        $this->assertEquals($expected, $headers->toArray());
    }

    public function test_adds_content_type_with_suffix(): void
    {
        $headers = Headers::create()
            ->withContentType(
                \VicidialApi\Enums\Transporter\ContentType::MULTIPART,
                '; boundary=----WebKitFormBoundary'
            );

        $expected = [
            'Content-Type' => 'multipart/form-data; boundary=----WebKitFormBoundary',
        ];

        $this->assertEquals($expected, $headers->toArray());
    }

    public function test_adds_custom_header(): void
    {
        $headers = Headers::create()
            ->withCustomHeader('X-Request-ID', 'abc-123-def');

        $expected = [
            'X-Request-ID' => 'abc-123-def',
        ];

        $this->assertEquals($expected, $headers->toArray());
    }

    public function test_chains_multiple_headers(): void
    {
        $apiKey = ApiKey::from('test_key');
        $headers = Headers::withAuthorization($apiKey)
            ->withContentType(\VicidialApi\Enums\Transporter\ContentType::JSON)
            ->withCustomHeader('X-Custom-Header', 'custom-value');

        $expected = [
            'Authorization' => 'Bearer test_key',
            'Content-Type' => 'application/json',
            'X-Custom-Header' => 'custom-value',
        ];

        $this->assertEquals($expected, $headers->toArray());
    }

    public function test_immutability(): void
    {
        $original = Headers::create()->withCustomHeader('X-Test', 'value1');
        $modified = $original->withCustomHeader('X-Test-2', 'value2');

        // Original should not be changed
        $this->assertEquals(['X-Test' => 'value1'], $original->toArray());
        $this->assertEquals(
            ['X-Test' => 'value1', 'X-Test-2' => 'value2'],
            $modified->toArray()
        );
    }

    public function test_bearer_token_format(): void
    {
        $apiKey = ApiKey::from('sk-1234567890abcdef');
        $headers = Headers::withAuthorization($apiKey);

        $authHeader = $headers->toArray()['Authorization'];

        $this->assertStringStartsWith('Bearer ', $authHeader);
        $this->assertStringContainsString('sk-1234567890abcdef', $authHeader);
    }

    public function test_basic_auth_format(): void
    {
        $basicAuth = BasicAuth::from('user', 'pass');
        $headers = Headers::withBasicAuthorization($basicAuth);

        $authHeader = $headers->toArray()['Authorization'];

        $this->assertStringStartsWith('Basic ', $authHeader);
    }

    public function test_empty_username_and_password(): void
    {
        $basicAuth = BasicAuth::from('', '');
        $encoded = $basicAuth->encode();
        $decoded = base64_decode($encoded);

        $this->assertEquals(':', $decoded);
    }
}
