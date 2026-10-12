# Reliability Features Guide

## Overview

The Vicidial API Wrapper includes enterprise-grade reliability features to handle production scenarios gracefully:

- **Configurable Timeouts**: Separate connect and request timeouts
- **Automatic Retries**: Exponential backoff with jitter for transient failures
- **Observability**: PSR-3 compatible logging for request tracking and debugging

These features help prevent outages and make debugging production issues much easier.

## Timeouts

### Connect Timeout vs Request Timeout

**Connect Timeout**: Maximum time to establish a connection to the server
**Request Timeout**: Maximum time for the entire request/response cycle

```php
use VicidialApi\VicidialConfig;
use VicidialApi\VicidialClient;

$config = VicidialConfig::create(
    host: 'vicidial.example.com',
    user: 'api_user',
    pass: 'api_password',
    timeout: 60,           // Total request timeout (seconds)
    connectTimeout: 10,    // Connection timeout (seconds)
);

$client = VicidialClient::make($config);
```

### Best Practices

- **Connect Timeout**: 5-10 seconds is usually sufficient
- **Request Timeout**: Depends on your operations:
  - Simple queries: 30 seconds
  - Complex operations: 60-120 seconds
  - Bulk operations: 180+ seconds

### Example: Different Timeouts for Different Operations

```php
// Quick operations - aggressive timeouts
$quickConfig = VicidialConfig::create(
    host: 'vicidial.example.com',
    user: 'api_user',
    pass: 'api_password',
    timeout: 30,
    connectTimeout: 5,
);

// Bulk operations - generous timeouts
$bulkConfig = VicidialConfig::create(
    host: 'vicidial.example.com',
    user: 'api_user',
    pass: 'api_password',
    timeout: 180,
    connectTimeout: 10,
);
```

## Automatic Retries

### Why Retries Matter

Network issues and temporary server problems are inevitable in production:
- Transient network errors
- Server temporarily overloaded (503)
- Rate limiting (429)
- Connection timeouts

Automatic retries with exponential backoff can handle these gracefully without code changes.

### Enabling Retries

```php
use VicidialApi\VicidialConfig;
use VicidialApi\VicidialClient;
use VicidialApi\ValueObjects\RetryConfig;

// Default retry configuration (recommended)
$config = VicidialConfig::create(
    host: 'vicidial.example.com',
    user: 'api_user',
    pass: 'api_password',
    retryConfig: RetryConfig::default(), // 3 attempts, exponential backoff
);

$client = VicidialClient::make($config);
```

### Default Retry Behavior

When you use `RetryConfig::default()`:
- **Max attempts**: 3 (1 initial + 2 retries)
- **Initial delay**: 1 second
- **Max delay**: 10 seconds
- **Backoff**: Exponential with 2x multiplier + random jitter
- **Retries on**: 5xx errors, 429 (rate limit), network errors

**Retry Schedule Example**:
- Attempt 1: Immediate
- Attempt 2: ~1 second delay (1000ms + jitter)
- Attempt 3: ~2 second delay (2000ms + jitter)

### Custom Retry Configuration

```php
use VicidialApi\ValueObjects\RetryConfig;

// Custom retry settings
$retryConfig = RetryConfig::custom(
    maxAttempts: 5,              // Try up to 5 times
    initialDelayMs: 500,         // Start with 500ms delay
    maxDelayMs: 30000,           // Cap at 30 seconds
    multiplier: 1.5,             // Slower growth (1.5x instead of 2x)
    retryOn5xx: true,            // Retry on server errors
    retryOnNetworkErrors: true,  // Retry on connection failures
    retryOn429: true,            // Retry on rate limits
);

$config = VicidialConfig::create(
    host: 'vicidial.example.com',
    user: 'api_user',
    pass: 'api_password',
    retryConfig: $retryConfig,
);
```

### Disabling Retries

```php
// Explicitly disable retries
$config = VicidialConfig::create(
    host: 'vicidial.example.com',
    user: 'api_user',
    pass: 'api_password',
    retryConfig: RetryConfig::disabled(),
);

// Or simply omit retryConfig (disabled by default)
$config = VicidialConfig::create(
    host: 'vicidial.example.com',
    user: 'api_user',
    pass: 'api_password',
);
```

### What Gets Retried?

By default, the wrapper retries:

1. **5xx Server Errors** (500, 502, 503, 504)
   - Server temporarily down or overloaded
   - Gateway timeouts

2. **429 Too Many Requests**
   - Rate limiting
   - Quota exceeded

3. **Network Errors**
   - Connection refused
   - Connection timeout
   - DNS resolution failures
   - SSL/TLS handshake failures

### What Doesn't Get Retried?

- **4xx Client Errors** (except 429)
  - 400 Bad Request
  - 401 Unauthorized
  - 404 Not Found
  - These indicate programming errors, not transient issues

### Exponential Backoff with Jitter

The retry mechanism uses exponential backoff to avoid overwhelming a recovering server:

```
Delay = min(initial_delay * (multiplier ^ attempt), max_delay) + random_jitter
```

**Jitter** (0-25% random variation) prevents thundering herd problems when many clients retry simultaneously.

Example with default settings:
```
Attempt 1: 0ms (immediate)
Attempt 2: 1000-1250ms (1s + 0-250ms jitter)
Attempt 3: 2000-2500ms (2s + 0-500ms jitter)
Attempt 4: 4000-5000ms (would be attempted if maxAttempts was 4+)
```

## Logging and Observability

### Why Logging Matters

In production, you need visibility into:
- Which API calls are being made
- How long they take
- When they fail and why
- Whether retries are happening

### PSR-3 Logger Support

The wrapper supports any PSR-3 compatible logger (Monolog, etc.):

```php
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\RotatingFileHandler;
use VicidialApi\VicidialConfig;
use VicidialApi\VicidialClient;

// Create a Monolog logger
$logger = new Logger('vicidial-api');
$logger->pushHandler(new StreamHandler('logs/vicidial.log', Logger::DEBUG));
$logger->pushHandler(new RotatingFileHandler('logs/vicidial-errors.log', 7, Logger::ERROR));

// Pass logger to config
$config = VicidialConfig::create(
    host: 'vicidial.example.com',
    user: 'api_user',
    pass: 'api_password',
    logger: $logger,
);

$client = VicidialClient::make($config);

// All API calls are now logged automatically
$response = $client->agent()->hangup('agent001');
```

### What Gets Logged?

#### DEBUG Level: Request Initiation
```json
{
  "message": "Vicidial API request initiated",
  "context": {
    "method": "GET",
    "uri": "http://vicidial.example.com/agc/api.php",
    "params": {
      "user": "api_user",
      "pass": "***REDACTED***",
      "source": "test",
      "function": "external_hangup",
      "agent_user": "agent001"
    }
  }
}
```

#### DEBUG Level: Retry Attempts
```json
{
  "message": "Vicidial API request",
  "context": {
    "attempt": 2,
    "max_attempts": 3
  }
}
```

#### INFO Level: Successful Response
```json
{
  "message": "Vicidial API request successful",
  "context": {
    "attempt": 1,
    "status_code": 200,
    "duration_ms": 245.67
  }
}
```

#### INFO Level: Retry Decision
```json
{
  "message": "Retrying Vicidial API request",
  "context": {
    "attempt": 1,
    "next_attempt": 2,
    "delay_ms": 1234,
    "reason": "HTTP 503"
  }
}
```

#### WARNING Level: HTTP Errors
```json
{
  "message": "Vicidial API request failed with HTTP error",
  "context": {
    "attempt": 1,
    "status_code": 503,
    "duration_ms": 5432.10,
    "message": "Service Unavailable"
  }
}
```

#### ERROR Level: Network Errors
```json
{
  "message": "Vicidial API request failed with network error",
  "context": {
    "attempt": 1,
    "duration_ms": 10001.5,
    "error": "Connection timeout after 10000ms"
  }
}
```

#### ERROR Level: All Retries Exhausted
```json
{
  "message": "Vicidial API request failed after all retries",
  "context": {
    "attempts": 3,
    "error": "503 Service Unavailable"
  }
}
```

### Sensitive Data Protection

The wrapper **automatically redacts sensitive parameters** from logs:
- `pass` / `password`
- `api_key`
- `auth` / `token`
- `secret`

These appear as `***REDACTED***` in log output.

### Example: Comprehensive Setup

```php
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SlackWebhookHandler;
use Monolog\Formatter\JsonFormatter;
use VicidialApi\VicidialConfig;
use VicidialApi\VicidialClient;
use VicidialApi\ValueObjects\RetryConfig;

// Setup logger with multiple handlers
$logger = new Logger('vicidial-api');

// File handler for all logs
$fileHandler = new StreamHandler('logs/vicidial.log', Logger::DEBUG);
$fileHandler->setFormatter(new JsonFormatter());
$logger->pushHandler($fileHandler);

// Slack notification for errors
$slackHandler = new SlackWebhookHandler(
    'https://hooks.slack.com/services/YOUR/WEBHOOK/URL',
    null,
    'vicidial-bot',
    true,
    null,
    Logger::ERROR
);
$logger->pushHandler($slackHandler);

// Create config with all reliability features
$config = VicidialConfig::create(
    host: 'vicidial.example.com',
    user: 'api_user',
    pass: 'api_password',
    protocol: 'https',
    timeout: 60,
    connectTimeout: 10,
    retryConfig: RetryConfig::default(),
    logger: $logger,
);

$client = VicidialClient::make($config);

try {
    $response = $client->agent()->updateFields('agent001', [
        'phone_number' => '5551234567',
        'first_name' => 'John',
    ]);
} catch (\Exception $e) {
    // Error is already logged, handle it appropriately
    echo "Failed after retries: " . $e->getMessage();
}
```

## Production Best Practices

### 1. Always Use Timeouts

```php
// ❌ Bad: Using default timeout might be too short or too long
$config = VicidialConfig::create(/* ... */);

// ✅ Good: Explicitly set timeouts based on your needs
$config = VicidialConfig::create(
    host: 'vicidial.example.com',
    user: 'api_user',
    pass: 'api_password',
    timeout: 45,
    connectTimeout: 8,
);
```

### 2. Enable Retries for Production

```php
// ✅ Production-ready configuration
$config = VicidialConfig::create(
    host: 'vicidial.example.com',
    user: 'api_user',
    pass: 'api_password',
    retryConfig: RetryConfig::default(),
    logger: $logger,
);
```

### 3. Use Logging in Production

```php
// ✅ Always use logging in production
$config = VicidialConfig::create(
    host: 'vicidial.example.com',
    user: 'api_user',
    pass: 'api_password',
    logger: $logger, // PSR-3 logger
);
```

### 4. Monitor Your Logs

Set up alerts for:
- High error rates
- Frequent retries (might indicate server issues)
- Long request durations
- Specific error patterns

### 5. Circuit Breaker Pattern (Advanced)

For high-traffic applications, consider implementing a circuit breaker:

```php
class CircuitBreaker
{
    private int $failures = 0;
    private const THRESHOLD = 5;
    private const TIMEOUT = 60; // seconds
    private ?int $openedAt = null;

    public function call(callable $fn)
    {
        if ($this->isOpen()) {
            throw new \Exception('Circuit breaker is open');
        }

        try {
            $result = $fn();
            $this->recordSuccess();
            return $result;
        } catch (\Exception $e) {
            $this->recordFailure();
            throw $e;
        }
    }

    private function isOpen(): bool
    {
        if ($this->openedAt === null) {
            return false;
        }

        if (time() - $this->openedAt > self::TIMEOUT) {
            $this->reset();
            return false;
        }

        return true;
    }

    private function recordFailure(): void
    {
        $this->failures++;
        if ($this->failures >= self::THRESHOLD) {
            $this->openedAt = time();
        }
    }

    private function recordSuccess(): void
    {
        $this->failures = 0;
        $this->openedAt = null;
    }

    private function reset(): void
    {
        $this->failures = 0;
        $this->openedAt = null;
    }
}

// Usage
$circuitBreaker = new CircuitBreaker();
$circuitBreaker->call(fn() => $client->agent()->hangup('agent001'));
```

## Troubleshooting

### Requests Timing Out

1. **Check network connectivity**
2. **Increase timeout values**
   ```php
   $config = VicidialConfig::create(
       // ...
       timeout: 120, // Increase if operations are slow
       connectTimeout: 20,
   );
   ```
3. **Check Vicidial server load**
4. **Review logs for patterns**

### Too Many Retries

If you see excessive retries:

1. **Check Vicidial server health**
2. **Reduce retry attempts**
   ```php
   $retryConfig = RetryConfig::custom(
       maxAttempts: 2, // Only retry once
   );
   ```
3. **Increase delays between retries**
   ```php
   $retryConfig = RetryConfig::custom(
       initialDelayMs: 2000,  // Start with 2 seconds
       multiplier: 3.0,        // Grow faster
   );
   ```

### High Error Rates

1. **Check logs for error patterns**
2. **Verify API credentials**
3. **Check Vicidial server logs**
4. **Verify network/firewall rules**

## Summary

| Feature | Purpose | Default | Production Recommendation |
|---------|---------|---------|---------------------------|
| **Connect Timeout** | Connection establishment | 10s | 5-10s |
| **Request Timeout** | Total request time | 30s | 45-60s |
| **Retries** | Handle transient failures | Disabled | Enabled (3 attempts) |
| **Logging** | Observability | None | PSR-3 logger |

**Minimum Production Setup**:
```php
$config = VicidialConfig::create(
    host: getenv('VICIDIAL_HOST'),
    user: getenv('VICIDIAL_USER'),
    pass: getenv('VICIDIAL_PASS'),
    protocol: 'https',
    timeout: 60,
    connectTimeout: 10,
    retryConfig: RetryConfig::default(),
    logger: $monologLogger,
);
```

This configuration will handle 95% of production scenarios gracefully.
