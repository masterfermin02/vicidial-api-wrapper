<?php

namespace Tests\ValueObjects;

use PHPUnit\Framework\TestCase;
use VicidialApi\Exceptions\ConfigurationException;
use VicidialApi\ValueObjects\RetryConfig;

class RetryConfigTest extends TestCase
{
    public function test_disabled_retry_config(): void
    {
        $config = RetryConfig::disabled();

        $this->assertFalse($config->isEnabled());
    }

    public function test_default_retry_config(): void
    {
        $config = RetryConfig::default();

        $this->assertTrue($config->isEnabled());
        $this->assertEquals(3, $config->getMaxAttempts());
        $this->assertEquals(1000, $config->getInitialDelayMs());
        $this->assertEquals(10000, $config->getMaxDelayMs());
        $this->assertEquals(2.0, $config->getMultiplier());
        $this->assertTrue($config->shouldRetryOn5xx());
        $this->assertTrue($config->shouldRetryOnNetworkErrors());
        $this->assertTrue($config->shouldRetryOn429());
    }

    public function test_custom_retry_config(): void
    {
        $config = RetryConfig::custom(
            maxAttempts: 5,
            initialDelayMs: 500,
            maxDelayMs: 30000,
            multiplier: 1.5,
            retryOn5xx: false,
            retryOnNetworkErrors: true,
            retryOn429: false,
        );

        $this->assertTrue($config->isEnabled());
        $this->assertEquals(5, $config->getMaxAttempts());
        $this->assertEquals(500, $config->getInitialDelayMs());
        $this->assertEquals(30000, $config->getMaxDelayMs());
        $this->assertEquals(1.5, $config->getMultiplier());
        $this->assertFalse($config->shouldRetryOn5xx());
        $this->assertTrue($config->shouldRetryOnNetworkErrors());
        $this->assertFalse($config->shouldRetryOn429());
    }

    public function test_calculate_delay_with_exponential_backoff(): void
    {
        $config = RetryConfig::default();

        // First attempt should be around 1000ms (1s)
        $delay1 = $config->calculateDelay(1);
        $this->assertGreaterThanOrEqual(1000, $delay1);
        $this->assertLessThanOrEqual(1250, $delay1); // 1s + 25% jitter

        // Second attempt should be around 2000ms (2s)
        $delay2 = $config->calculateDelay(2);
        $this->assertGreaterThanOrEqual(2000, $delay2);
        $this->assertLessThanOrEqual(2500, $delay2); // 2s + 25% jitter

        // Third attempt should be around 4000ms (4s)
        $delay3 = $config->calculateDelay(3);
        $this->assertGreaterThanOrEqual(4000, $delay3);
        $this->assertLessThanOrEqual(5000, $delay3); // 4s + 25% jitter
    }

    public function test_calculate_delay_respects_max_delay(): void
    {
        $config = RetryConfig::custom(
            maxAttempts: 10,
            initialDelayMs: 1000,
            maxDelayMs: 5000,
            multiplier: 2.0,
        );

        // Even with high attempt numbers, should not exceed max delay + jitter
        $delay = $config->calculateDelay(10);
        $this->assertLessThanOrEqual(6250, $delay); // 5000 + 25% jitter
    }

    public function test_invalid_max_attempts(): void
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('maxAttempts must be at least 1');

        RetryConfig::custom(maxAttempts: 0);
    }

    public function test_invalid_initial_delay(): void
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('initialDelayMs must be non-negative');

        RetryConfig::custom(initialDelayMs: -100);
    }

    public function test_invalid_max_delay(): void
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('maxDelayMs must be greater than or equal to initialDelayMs');

        RetryConfig::custom(
            initialDelayMs: 5000,
            maxDelayMs: 1000,
        );
    }

    public function test_invalid_multiplier(): void
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('multiplier must be at least 1.0');

        RetryConfig::custom(multiplier: 0.5);
    }
}
