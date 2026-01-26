<?php

declare(strict_types=1);

namespace Tests\Unit\ValueObjects;

use PHPUnit\Framework\TestCase;
use VicidialApi\ValueObjects\Transporter\BaseUri;

/**
 * Unit tests for BaseUri value object
 * Tests URL building without making actual HTTP requests
 */
final class BaseUriTest extends TestCase
{
    public function test_creates_uri_from_domain(): void
    {
        $uri = BaseUri::from('example.com');

        $this->assertEquals('https://example.com/', $uri->toString());
    }

    public function test_preserves_http_protocol(): void
    {
        $uri = BaseUri::from('http://example.com');

        $this->assertEquals('http://example.com/', $uri->toString());
    }

    public function test_preserves_https_protocol(): void
    {
        $uri = BaseUri::from('https://example.com');

        $this->assertEquals('https://example.com/', $uri->toString());
    }

    public function test_defaults_to_https_when_no_protocol(): void
    {
        $uri = BaseUri::from('vicidial.example.com');

        $this->assertEquals('https://vicidial.example.com/', $uri->toString());
    }

    public function test_handles_ip_address(): void
    {
        $uri = BaseUri::from('192.168.1.100');

        $this->assertEquals('https://192.168.1.100/', $uri->toString());
    }

    public function test_handles_ip_address_with_port(): void
    {
        $uri = BaseUri::from('http://192.168.1.100:8080');

        $this->assertEquals('http://192.168.1.100:8080/', $uri->toString());
    }

    public function test_handles_localhost(): void
    {
        $uri = BaseUri::from('http://localhost');

        $this->assertEquals('http://localhost/', $uri->toString());
    }

    public function test_handles_subdomain(): void
    {
        $uri = BaseUri::from('api.vicidial.example.com');

        $this->assertEquals('https://api.vicidial.example.com/', $uri->toString());
    }
}
