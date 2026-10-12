<?php

use PHPUnit\Framework\TestCase;
use VicidialApi\Exceptions\ConfigurationException;
use VicidialApi\VicidialClient;
use VicidialApi\VicidialConfig;

class VicidialConfigTest extends TestCase
{
    public function test_create_with_defaults(): void
    {
        $config = VicidialConfig::create(
            host: 'localhost',
            user: 'testuser',
            pass: 'testpass'
        );

        $this->assertSame('localhost', $config->getHost());
        $this->assertSame('testuser', $config->getUser());
        $this->assertSame('testpass', $config->getPass());
        $this->assertSame('http', $config->getProtocol());
        $this->assertTrue($config->isVerifyTls());
        $this->assertSame(30, $config->getTimeout());
        $this->assertFalse($config->isUrlEncodeDefault());
        $this->assertSame('VicidialApi-PHP-Wrapper/4.1', $config->getUserAgent());
        $this->assertSame('test', $config->getSource());
        $this->assertFalse($config->hasBasicAuth());
        $this->assertSame('http://localhost', $config->getBaseUri());
    }

    public function test_create_with_custom_options(): void
    {
        $config = VicidialConfig::create(
            host: 'api.vicidial.com',
            user: 'apiuser',
            pass: 'apipass',
            protocol: 'https',
            verifyTls: false,
            timeout: 60,
            urlEncodeDefault: true,
            userAgent: 'MyApp/1.0',
            source: 'myapp'
        );

        $this->assertSame('api.vicidial.com', $config->getHost());
        $this->assertSame('https', $config->getProtocol());
        $this->assertFalse($config->isVerifyTls());
        $this->assertSame(60, $config->getTimeout());
        $this->assertTrue($config->isUrlEncodeDefault());
        $this->assertSame('MyApp/1.0', $config->getUserAgent());
        $this->assertSame('myapp', $config->getSource());
        $this->assertSame('https://api.vicidial.com', $config->getBaseUri());
    }

    public function test_create_with_basic_auth(): void
    {
        $config = VicidialConfig::createWithBasicAuth(
            host: 'localhost',
            user: 'testuser',
            pass: 'testpass',
            basicAuthUser: 'basicuser',
            basicAuthPass: 'basicpass'
        );

        $this->assertTrue($config->hasBasicAuth());
        $this->assertSame('basicuser', $config->getBasicAuthUser());
        $this->assertSame('basicpass', $config->getBasicAuthPass());
    }

    public function test_invalid_protocol_throws_exception(): void
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('Protocol must be either "http" or "https"');

        VicidialConfig::create(
            host: 'localhost',
            user: 'testuser',
            pass: 'testpass',
            protocol: 'ftp'
        );
    }

    public function test_invalid_timeout_throws_exception(): void
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('Timeout must be greater than 0');

        VicidialConfig::create(
            host: 'localhost',
            user: 'testuser',
            pass: 'testpass',
            timeout: 0
        );
    }

    public function test_vicidial_client_can_be_created_from_config(): void
    {
        $config = VicidialConfig::create(
            host: 'localhost',
            user: 'testuser',
            pass: 'testpass'
        );

        $client = VicidialClient::make($config);

        $this->assertInstanceOf(VicidialClient::class, $client);
    }

    public function test_vicidial_client_provides_resource_access(): void
    {
        $config = VicidialConfig::create(
            host: 'localhost',
            user: 'testuser',
            pass: 'testpass'
        );

        $client = VicidialClient::make($config);

        $this->assertInstanceOf(\VicidialApi\Resources\Agent::class, $client->agent());
        $this->assertInstanceOf(\VicidialApi\Resources\Admin::class, $client->admin());
        $this->assertInstanceOf(\VicidialApi\Resources\RemoteAgent::class, $client->remoteAgent());
    }
}
