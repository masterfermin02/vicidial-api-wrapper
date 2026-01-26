<?php

declare(strict_types=1);

namespace VicidialApi;

use Psr\Log\LoggerInterface;
use VicidialApi\Exceptions\ConfigurationException;
use VicidialApi\Logger\NullLogger;
use VicidialApi\ValueObjects\RetryConfig;

final class VicidialConfig
{
    public function __construct(
        private readonly string $host,
        private readonly string $user,
        private readonly string $pass,
        private readonly string $protocol = 'http',
        private readonly bool $verifyTls = true,
        private readonly int $timeout = 30,
        private readonly int $connectTimeout = 10,
        private readonly bool $urlEncodeDefault = false,
        private readonly string $userAgent = 'VicidialApi-PHP-Wrapper/2.0',
        private readonly ?string $basicAuthUser = null,
        private readonly ?string $basicAuthPass = null,
        private readonly string $source = 'test',
        private readonly ?RetryConfig $retryConfig = null,
        private readonly ?LoggerInterface $logger = null,
    ) {
        if (! in_array($protocol, ['http', 'https'])) {
            throw new ConfigurationException('Protocol must be either "http" or "https"');
        }

        if ($timeout <= 0) {
            throw new ConfigurationException('Timeout must be greater than 0');
        }

        if ($connectTimeout <= 0) {
            throw new ConfigurationException('Connect timeout must be greater than 0');
        }

        if ($connectTimeout > $timeout) {
            throw new ConfigurationException('Connect timeout cannot be greater than request timeout');
        }
    }

    public static function create(
        string $host,
        string $user,
        string $pass,
        string $protocol = 'http',
        bool $verifyTls = true,
        int $timeout = 30,
        int $connectTimeout = 10,
        bool $urlEncodeDefault = false,
        string $userAgent = 'VicidialApi-PHP-Wrapper/2.0',
        string $source = 'test',
        ?RetryConfig $retryConfig = null,
        ?LoggerInterface $logger = null,
    ): self {
        return new self(
            host: $host,
            user: $user,
            pass: $pass,
            protocol: $protocol,
            verifyTls: $verifyTls,
            timeout: $timeout,
            connectTimeout: $connectTimeout,
            urlEncodeDefault: $urlEncodeDefault,
            userAgent: $userAgent,
            source: $source,
            retryConfig: $retryConfig,
            logger: $logger,
        );
    }

    public static function createWithBasicAuth(
        string $host,
        string $user,
        string $pass,
        string $basicAuthUser,
        string $basicAuthPass,
        string $protocol = 'http',
        bool $verifyTls = true,
        int $timeout = 30,
        int $connectTimeout = 10,
        bool $urlEncodeDefault = false,
        string $userAgent = 'VicidialApi-PHP-Wrapper/2.0',
        string $source = 'test',
        ?RetryConfig $retryConfig = null,
        ?LoggerInterface $logger = null,
    ): self {
        return new self(
            host: $host,
            user: $user,
            pass: $pass,
            protocol: $protocol,
            verifyTls: $verifyTls,
            timeout: $timeout,
            connectTimeout: $connectTimeout,
            urlEncodeDefault: $urlEncodeDefault,
            userAgent: $userAgent,
            basicAuthUser: $basicAuthUser,
            basicAuthPass: $basicAuthPass,
            source: $source,
            retryConfig: $retryConfig,
            logger: $logger,
        );
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getUser(): string
    {
        return $this->user;
    }

    public function getPass(): string
    {
        return $this->pass;
    }

    public function getProtocol(): string
    {
        return $this->protocol;
    }

    public function isVerifyTls(): bool
    {
        return $this->verifyTls;
    }

    public function getTimeout(): int
    {
        return $this->timeout;
    }

    public function getConnectTimeout(): int
    {
        return $this->connectTimeout;
    }

    public function isUrlEncodeDefault(): bool
    {
        return $this->urlEncodeDefault;
    }

    public function getUserAgent(): string
    {
        return $this->userAgent;
    }

    public function getBasicAuthUser(): string
    {
        if ($this->basicAuthUser === null) {
            throw new ConfigurationException('Basic auth user is not set');
        }

        return $this->basicAuthUser;
    }

    public function getBasicAuthPass(): string
    {
        if ($this->basicAuthPass === null) {
            throw new ConfigurationException('Basic auth password is not set');
        }

        return $this->basicAuthPass;
    }

    public function hasBasicAuth(): bool
    {
        return $this->basicAuthUser !== null && $this->basicAuthPass !== null;
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function getRetryConfig(): RetryConfig
    {
        return $this->retryConfig ?? RetryConfig::disabled();
    }

    public function getLogger(): LoggerInterface
    {
        return $this->logger ?? new NullLogger;
    }

    public function hasLogger(): bool
    {
        return $this->logger instanceof \Psr\Log\LoggerInterface;
    }

    public function getBaseUri(): string
    {
        return $this->protocol.'://'.$this->host;
    }
}
