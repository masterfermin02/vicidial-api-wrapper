<?php

declare(strict_types=1);

namespace Tests;

use VicidialApi\VicidialConfig;

final class VicidialConfigFromArrayTest extends TestCase
{
    public function test_minimal_array_uses_defaults(): void
    {
        $config = VicidialConfig::fromArray(['host' => 'vicidial.test', 'user' => 'u', 'pass' => 'p']);

        $this->assertSame('http://vicidial.test', $config->getBaseUri());
        $this->assertSame(30, $config->getTimeout());
        $this->assertSame(10, $config->getConnectTimeout());
        $this->assertTrue($config->isVerifyTls());
        $this->assertSame(VicidialConfig::DEFAULT_SOURCE, $config->getSource());
        $this->assertSame(VicidialConfig::DEFAULT_USER_AGENT, $config->getUserAgent());
        $this->assertFalse($config->hasBasicAuth());
        $this->assertFalse($config->getRetryConfig()->isEnabled());
    }

    public function test_strings_from_env_are_coerced(): void
    {
        $config = VicidialConfig::fromArray([
            'host' => 'vicidial.test', 'user' => 'u', 'pass' => 'p',
            'protocol' => 'https', 'verify_tls' => 'false', 'timeout' => '60', 'connect_timeout' => '5',
            'user_agent' => null, 'source' => '',
            'retry' => ['enabled' => '1', 'max_attempts' => '4', 'multiplier' => '1.5'],
        ]);

        $this->assertSame('https', $config->getProtocol());
        $this->assertFalse($config->isVerifyTls());
        $this->assertSame(60, $config->getTimeout());
        $this->assertSame(5, $config->getConnectTimeout());
        $this->assertSame(VicidialConfig::DEFAULT_USER_AGENT, $config->getUserAgent());
        $this->assertSame(VicidialConfig::DEFAULT_SOURCE, $config->getSource());
        $this->assertTrue($config->getRetryConfig()->isEnabled());
        $this->assertSame(4, $config->getRetryConfig()->getMaxAttempts());
        $this->assertSame(1.5, $config->getRetryConfig()->getMultiplier());
    }

    public function test_basic_auth_requires_both_values(): void
    {
        $half = VicidialConfig::fromArray(['host' => 'h', 'user' => 'u', 'pass' => 'p', 'basic_auth' => ['user' => 'web', 'pass' => null]]);
        $this->assertFalse($half->hasBasicAuth());

        $full = VicidialConfig::fromArray(['host' => 'h', 'user' => 'u', 'pass' => 'p', 'basic_auth' => ['user' => 'web', 'pass' => 'pw']]);
        $this->assertTrue($full->hasBasicAuth());
        $this->assertSame('pw', $full->getBasicAuthPass());
    }
}
