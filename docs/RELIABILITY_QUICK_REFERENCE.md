# Reliability Features Quick Reference

## 🚀 Quick Start

```php
use VicidialApi\VicidialConfig;
use VicidialApi\VicidialClient;
use VicidialApi\ValueObjects\RetryConfig;

// Production-ready configuration
$config = VicidialConfig::create(
    host: 'vicidial.example.com',
    user: 'api_user',
    pass: 'api_password',
    protocol: 'https',
    timeout: 60,                      // Request timeout: 60s
    connectTimeout: 10,               // Connect timeout: 10s
    retryConfig: RetryConfig::default(), // Enable retries
    logger: $monologLogger,           // Optional: PSR-3 logger
);

$client = VicidialClient::make($config);
```

## ⏱️ Timeouts

| Parameter | Default | Recommended | Purpose |
|-----------|---------|-------------|---------|
| `timeout` | 30s | 60s | Total request time |
| `connectTimeout` | 10s | 5-10s | Connection establishment |

```php
// Quick operations
$config = VicidialConfig::create(
    timeout: 30,
    connectTimeout: 5,
    // ...
);

// Bulk operations
$config = VicidialConfig::create(
    timeout: 180,
    connectTimeout: 10,
    // ...
);
```

## 🔄 Retry Configurations

### Default (Recommended)
```php
retryConfig: RetryConfig::default()
```
- Max attempts: 3
- Initial delay: 1s
- Max delay: 10s
- Multiplier: 2x
- Retries: 5xx, 429, network errors

### Disabled
```php
retryConfig: RetryConfig::disabled()
// or omit the parameter
```

### Custom
```php
retryConfig: RetryConfig::custom(
    maxAttempts: 5,
    initialDelayMs: 500,
    maxDelayMs: 30000,
    multiplier: 1.5,
    retryOn5xx: true,
    retryOnNetworkErrors: true,
    retryOn429: true,
)
```

## 📝 Logging Setup

### Monolog (Recommended)
```php
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\RotatingFileHandler;

$logger = new Logger('vicidial-api');

// All requests (JSON format)
$logger->pushHandler(new StreamHandler('logs/vicidial.log', Logger::DEBUG));

// Errors only (rotate daily, keep 7 days)
$logger->pushHandler(new RotatingFileHandler('logs/vicidial-errors.log', 7, Logger::ERROR));

$config = VicidialConfig::create(
    logger: $logger,
    // ...
);
```

## 🎯 Common Scenarios

### Critical Operations (Aggressive Retry)
```php
$config = VicidialConfig::create(
    timeout: 30,
    connectTimeout: 5,
    retryConfig: RetryConfig::custom(
        maxAttempts: 5,
        initialDelayMs: 500,
        multiplier: 1.5,
    ),
);
```

### Bulk Operations (Conservative Retry)
```php
$config = VicidialConfig::create(
    timeout: 180,
    connectTimeout: 10,
    retryConfig: RetryConfig::custom(
        maxAttempts: 3,
        initialDelayMs: 2000,
        multiplier: 3.0,
    ),
);
```

### Real-time Operations (No Retry)
```php
$config = VicidialConfig::create(
    timeout: 15,
    connectTimeout: 3,
    retryConfig: RetryConfig::disabled(),
);
```

### Write Operations (no duplicate inserts)
VICIdial has no idempotency keys: retrying `add_lead` after a timeout can insert the lead twice.
Retry only when the server definitely rejected the request:
```php
$config = VicidialConfig::create(
    retryConfig: RetryConfig::custom(
        retryOn5xx: true,
        retryOn429: true,
        retryOnNetworkErrors: false,   // timeout = outcome unknown, do not repeat writes
    ),
);
```

### Network Errors Only
```php
$config = VicidialConfig::create(
    retryConfig: RetryConfig::custom(
        retryOn5xx: false,
        retryOnNetworkErrors: true,
        retryOn429: false,
    ),
);
```

## 🔍 What Gets Logged?

### DEBUG
- Request initiation with sanitized params
- Retry attempt numbers

### INFO
- Successful responses (status, duration)
- Retry decisions (delay, reason)

### WARNING
- HTTP errors (4xx/5xx)

### ERROR
- Network errors
- All retries exhausted

## 🛡️ Sensitive Data Protection

Automatically redacted in logs:
- `pass`, `password`
- `api_key`
- `auth`, `token`
- `secret`

Shows as: `***REDACTED***`

## ⚡ What Gets Retried?

✅ **Retried by Default**:
- 5xx server errors (500, 502, 503, 504)
- 429 Too Many Requests (rate limiting)
- Network failures (timeout, connection refused, DNS)

❌ **Never Retried**:
- 4xx client errors (except 429)
- 400 Bad Request
- 401 Unauthorized
- 404 Not Found

## 📊 Exponential Backoff Schedule

Default configuration (3 attempts):
```
Attempt 1: Immediate
Attempt 2: ~1s delay (1000ms + jitter)
Attempt 3: ~2s delay (2000ms + jitter)
```

Custom (5 attempts, 500ms initial):
```
Attempt 1: Immediate
Attempt 2: ~500ms
Attempt 3: ~750ms
Attempt 4: ~1125ms
Attempt 5: ~1687ms
```

## 🚨 Error Handling

```php
use VicidialApi\Exceptions\TransporterException;

try {
    $response = $client->agent()->hangup('agent001');
} catch (TransporterException $e) {
    // Network/HTTP error after all retries
    // Error is already logged
    error_log("Failed after retries: " . $e->getMessage());
} catch (Exception $e) {
    // Other errors (programming errors, etc.)
    error_log("Error: " . $e->getMessage());
}
```

## 📈 Monitoring & Alerts

### Recommended Alerts
- **Error rate > 5%**: Investigate server/network
- **Retry rate > 30%**: Server may be struggling
- **Avg duration > 10s**: Timeout settings may need adjustment
- **Specific error patterns**: Target root cause

### Log Analysis
```bash
# Count errors
grep "ERROR" logs/vicidial.log | wc -l

# Find retry patterns
grep "Retrying" logs/vicidial.log

# Slow requests (>5s)
jq 'select(.context.duration_ms > 5000)' logs/vicidial.log
```

## 🎓 Best Practices

1. ✅ **Always use timeouts** - Set explicitly based on operation type
2. ✅ **Enable retries in production** - Use `RetryConfig::default()`
3. ✅ **Use logging** - Essential for debugging production issues
4. ✅ **Monitor your logs** - Set up alerts for error patterns
5. ✅ **Test your configuration** - Verify timeouts/retries work as expected

## 📚 See Also

- [RELIABILITY_FEATURES.md](RELIABILITY_FEATURES.md) - Complete guide
- [example/reliability_features.php](../example/reliability_features.php) - Working example
- [example/custom_retry.php](../example/custom_retry.php) - Custom configurations

## 💡 Pro Tips

- Start with defaults, customize only if needed
- Longer timeouts for bulk operations
- Use structured logging (JSON) for easier parsing
- Rotate log files to manage disk space
- Test retry behavior in staging first
- Monitor retry rates - high rates indicate problems
- Use separate configs for different operation types

---

**Need Help?** See the full [Reliability Features Guide](RELIABILITY_FEATURES.md)
