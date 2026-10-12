<?php

declare(strict_types=1);

namespace VicidialApi;

use Psr\Log\LoggerInterface;
use VicidialApi\Exceptions\ConfigurationException;
use VicidialApi\Logger\NullLogger;
use VicidialApi\ValueObjects\RetryConfig;

final class VicidialConfig
{
    /**
     * Default `source` query parameter: identifies this wrapper in VICIdial logs (API limit is 20 characters).
     */
    public const DEFAULT_SOURCE = 'vicidial-api-wrapper';

    public const DEFAULT_USER_AGENT = 'VicidialApi-PHP-Wrapper/4.2';

    public function __construct(
        private readonly string $host,
        private readonly string $user,
        private readonly string $pass,
        private readonly string $protocol = 'http',
        private readonly bool $verifyTls = true,
        private readonly int $timeout = 30,
        private readonly int $connectTimeout = 10,
        private readonly bool $urlEncodeDefault = false,
        private readonly string $userAgent = self::DEFAULT_USER_AGENT,
        private readonly ?string $basicAuthUser = null,
        private readonly ?string $basicAuthPass = null,
        private readonly string $source = self::DEFAULT_SOURCE,
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
        string $userAgent = self::DEFAULT_USER_AGENT,
        string $source = self::DEFAULT_SOURCE,
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
        string $userAgent = self::DEFAULT_USER_AGENT,
        string $source = self::DEFAULT_SOURCE,
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

    /**
     * Build a configuration from a plain array, as read from a config file or environment.
     *
     * Keys (snake_case, all optional except host/user/pass): host, user, pass, protocol, verify_tls,
     * timeout, connect_timeout, source, user_agent, basic_auth => [user, pass],
     * retry => [enabled, max_attempts, initial_delay_ms, max_delay_ms, multiplier, retry_on_5xx,
     * retry_on_network_errors, retry_on_429]. Unknown keys are ignored. This is what the Laravel
     * service provider feeds from config/vicidial.php.
     *
     * @param  array<string, mixed>  $config
     */
    public static function fromArray(array $config, ?LoggerInterface $logger = null): self
    {
        $string = static fn (mixed $value, string $default = ''): string => is_scalar($value) ? (string) $value : $default;
        $int = static fn (mixed $value, int $default): int => is_numeric($value) ? (int) $value : $default;
        $bool = static fn (mixed $value, bool $default): bool => is_bool($value) ? $value : (is_scalar($value) ? filter_var($value, FILTER_VALIDATE_BOOL) : $default);

        /** @var array<string, mixed> $basicAuth */
        $basicAuth = is_array($config['basic_auth'] ?? null) ? $config['basic_auth'] : [];
        $basicAuthUser = $string($basicAuth['user'] ?? null);
        $basicAuthPass = $string($basicAuth['pass'] ?? null);

        /** @var array<string, mixed> $retry */
        $retry = is_array($config['retry'] ?? null) ? $config['retry'] : [];
        $retryConfig = $bool($retry['enabled'] ?? false, false)
            ? RetryConfig::custom(
                maxAttempts: $int($retry['max_attempts'] ?? null, 3),
                initialDelayMs: $int($retry['initial_delay_ms'] ?? null, 1000),
                maxDelayMs: $int($retry['max_delay_ms'] ?? null, 10000),
                multiplier: is_numeric($retry['multiplier'] ?? null) ? (float) $retry['multiplier'] : 2.0,
                retryOn5xx: $bool($retry['retry_on_5xx'] ?? true, true),
                retryOnNetworkErrors: $bool($retry['retry_on_network_errors'] ?? true, true),
                retryOn429: $bool($retry['retry_on_429'] ?? true, true),
            )
            : RetryConfig::disabled();

        return new self(
            host: $string($config['host'] ?? null),
            user: $string($config['user'] ?? null),
            pass: $string($config['pass'] ?? null),
            protocol: $string($config['protocol'] ?? null, 'http'),
            verifyTls: $bool($config['verify_tls'] ?? true, true),
            timeout: $int($config['timeout'] ?? null, 30),
            connectTimeout: $int($config['connect_timeout'] ?? null, 10),
            userAgent: $string($config['user_agent'] ?? null, self::DEFAULT_USER_AGENT) ?: self::DEFAULT_USER_AGENT,
            basicAuthUser: $basicAuthUser !== '' ? $basicAuthUser : null,
            basicAuthPass: $basicAuthPass !== '' ? $basicAuthPass : null,
            source: $string($config['source'] ?? null, self::DEFAULT_SOURCE) ?: self::DEFAULT_SOURCE,
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

    /**
     * @deprecated Has no effect. Query values are always URL-encoded exactly once by the
     *             transport (http_build_query); do not pre-encode values yourself.
     */
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
        return $this->logger instanceof LoggerInterface;
    }

    public function getBaseUri(): string
    {
        return $this->protocol.'://'.$this->host;
    }
}
