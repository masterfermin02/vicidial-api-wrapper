<?php

declare(strict_types=1);

namespace VicidialApi\ValueObjects;

use VicidialApi\Exceptions\ConfigurationException;

/**
 * Configuration for retry behavior.
 */
final class RetryConfig
{
    public function __construct(
        private readonly bool $enabled = false,
        private readonly int $maxAttempts = 3,
        private readonly int $initialDelayMs = 1000,
        private readonly int $maxDelayMs = 10000,
        private readonly float $multiplier = 2.0,
        private readonly bool $retryOn5xx = true,
        private readonly bool $retryOnNetworkErrors = true,
        private readonly bool $retryOn429 = true,
    ) {
        if ($maxAttempts < 1) {
            throw new ConfigurationException('maxAttempts must be at least 1');
        }

        if ($initialDelayMs < 0) {
            throw new ConfigurationException('initialDelayMs must be non-negative');
        }

        if ($maxDelayMs < $initialDelayMs) {
            throw new ConfigurationException('maxDelayMs must be greater than or equal to initialDelayMs');
        }

        if ($multiplier < 1.0) {
            throw new ConfigurationException('multiplier must be at least 1.0');
        }
    }

    /**
     * Create a disabled retry configuration.
     */
    public static function disabled(): self
    {
        return new self(enabled: false);
    }

    /**
     * Create a default retry configuration.
     * Retries up to 3 times with exponential backoff starting at 1 second.
     */
    public static function default(): self
    {
        return new self(enabled: true);
    }

    /**
     * Create a custom retry configuration.
     */
    public static function custom(
        int $maxAttempts = 3,
        int $initialDelayMs = 1000,
        int $maxDelayMs = 10000,
        float $multiplier = 2.0,
        bool $retryOn5xx = true,
        bool $retryOnNetworkErrors = true,
        bool $retryOn429 = true,
    ): self {
        return new self(
            enabled: true,
            maxAttempts: $maxAttempts,
            initialDelayMs: $initialDelayMs,
            maxDelayMs: $maxDelayMs,
            multiplier: $multiplier,
            retryOn5xx: $retryOn5xx,
            retryOnNetworkErrors: $retryOnNetworkErrors,
            retryOn429: $retryOn429,
        );
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function getMaxAttempts(): int
    {
        return $this->maxAttempts;
    }

    public function getInitialDelayMs(): int
    {
        return $this->initialDelayMs;
    }

    public function getMaxDelayMs(): int
    {
        return $this->maxDelayMs;
    }

    public function getMultiplier(): float
    {
        return $this->multiplier;
    }

    public function shouldRetryOn5xx(): bool
    {
        return $this->retryOn5xx;
    }

    public function shouldRetryOnNetworkErrors(): bool
    {
        return $this->retryOnNetworkErrors;
    }

    public function shouldRetryOn429(): bool
    {
        return $this->retryOn429;
    }

    /**
     * Calculate the delay in milliseconds for a given attempt number.
     * Uses exponential backoff with jitter.
     */
    public function calculateDelay(int $attemptNumber): int
    {
        $delay = (int) ($this->initialDelayMs * pow($this->multiplier, $attemptNumber - 1));
        $delay = min($delay, $this->maxDelayMs);

        // Add jitter (random value between 0 and 25% of delay)
        $jitter = (int) ($delay * 0.25 * (mt_rand() / mt_getrandmax()));

        return $delay + $jitter;
    }
}
