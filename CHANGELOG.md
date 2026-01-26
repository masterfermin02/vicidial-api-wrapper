# Changelog

All notable changes to the Vicidial API Wrapper will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.0.0] - 2026-01-26

### Added - Major Features

#### Reliability Features 🛡️
- **Configurable Timeouts**: Separate connect timeout (default: 10s) and request timeout (default: 30s)
  - `VicidialConfig::create(..., timeout: 60, connectTimeout: 10)`
  - Prevents hung requests and improves resilience
  
- **Automatic Retries with Exponential Backoff**: Handle transient failures gracefully
  - New `RetryConfig` value object for retry configuration
  - `RetryConfig::default()` - 3 attempts with exponential backoff
  - `RetryConfig::custom()` - Full customization
  - `RetryConfig::disabled()` - Explicit opt-out
  - Retries 5xx errors, 429 rate limits, and network failures
  - Exponential backoff with jitter (prevents thundering herd)
  - Per-attempt logging for observability
  
- **PSR-3 Logger Integration**: Pluggable logging for production observability
  - Added `psr/log:^3.0` dependency
  - `NullLogger` included for no-op behavior (default)
  - Automatic sensitive data redaction (passwords, API keys, etc.)
  - Comprehensive logging at DEBUG, INFO, WARNING, ERROR levels
  - Request metrics: duration, status codes, retry attempts
  - Sanitized parameter logging for security

#### Configuration System Enhancements
- Enhanced `VicidialConfig` with new parameters:
  - `connectTimeout` - Connection establishment timeout
  - `retryConfig` - Retry behavior configuration
  - `logger` - PSR-3 compatible logger instance
- Validation ensures connect timeout ≤ request timeout

#### Method Naming Conventions
- All methods use consistent **camelCase** naming
- Clear mapping from legacy Vicidial API snake_case functions
- Examples: `updateFields()`, `pauseCode()`, `mohList()`, `addLead()`
- Response DTO variants follow same convention with `Response` suffix

### Added - Documentation

#### Comprehensive Guides
- **RELIABILITY_FEATURES.md** (500+ lines) - Complete reliability features guide
  - Timeout configuration
  - Retry strategies
  - Logging setup
  - Production best practices
  - Troubleshooting guide
  
- **RELIABILITY_QUICK_REFERENCE.md** - Quick reference cheat sheet
  - Common configurations
  - Code snippets
  - Monitoring tips
  
- **RELIABILITY_IMPLEMENTATION.md** - Technical implementation details
  - Architecture decisions
  - Performance considerations
  - Testing strategy
  
- **METHOD_NAMING_CONVENTIONS.md** - Complete method naming guide
  - All methods catalogued
  - Legacy API mapping table
  - Best practices for adding new methods
  
- **NAMING_CONVENTIONS_IMPLEMENTATION.md** - Naming implementation summary
  
- **COMPLETE_FEATURE_SUMMARY.md** - v2.0 feature overview

#### Examples
- **example/reliability_features.php** - Production-ready setup example
  - Monolog integration
  - Complete configuration
  - Error handling
  
- **example/custom_retry.php** - Custom retry configuration examples
  - Aggressive retry for critical operations
  - Conservative retry for bulk operations
  - Network-only retry
  - Fast-fail mode

### Added - Tests
- **RetryConfigTest.php** - Comprehensive unit tests for retry configuration
  - Configuration validation
  - Exponential backoff calculation
  - Edge cases and error handling

### Changed
- `VicidialClient` now passes `connectTimeout` to Guzzle HTTP client
- `HttpTransporter` enhanced with retry logic and comprehensive logging
- README updated with reliability features section and production examples

### Enhanced
- `HttpTransporter::sendRequest()` - Implements retry loop with backoff
- Request logging with automatic parameter sanitization
- Error handling with detailed context logging

### Fixed
- README examples now use correct method names (`inactive()` not `inActive()`)

### Security
- Automatic redaction of sensitive parameters in logs:
  - `pass`, `password`
  - `api_key`
  - `auth`, `token`
  - `secret`
  - Appears as `***REDACTED***` in logs

### Backward Compatibility
- **100% backward compatible** - All new features are opt-in
- Existing code works without modifications
- Retries **disabled by default** (must explicitly enable)
- Legacy factory methods still supported (but deprecated)

### Performance
- Minimal overhead for successful requests
- Retry delays only apply to failed requests (expected behavior)
- Logging overhead only when logger provided
- No memory leaks or state accumulation

### Dependencies
- Added: `psr/log: ^3.0` - PSR-3 Logger Interface

---

## [1.x.x] - Previous Versions

See git history for changes in version 1.x releases.

---

## Migration Guide

### From v1.x to v2.0

#### No Breaking Changes ✅
All v1.x code continues to work in v2.0 without modifications.

#### Recommended Upgrades

**Add Reliability Features** (opt-in):
```php
// Before (v1.x)
$client = VicidialApi::create($host, $user, $pass);

// After (v2.0 with reliability)
use VicidialApi\VicidialConfig;
use VicidialApi\VicidialClient;
use VicidialApi\ValueObjects\RetryConfig;

$config = VicidialConfig::create(
    host: $host,
    user: $user,
    pass: $pass,
    protocol: 'https',
    timeout: 60,
    connectTimeout: 10,
    retryConfig: RetryConfig::default(),
    logger: $logger,
);

$client = VicidialClient::make($config);
```

**Adopt Response DTOs** (opt-in):
```php
// Before (string parsing)
$result = $client->agent()->updateFields($agent, $fields);
if (strpos($result, 'SUCCESS') !== false) {
    // parse result manually
}

// After (structured response)
$response = $client->agent()->updateFieldsResponse($agent, $fields);
if ($response->ok()) {
    $leadId = $response->get('lead_id');
}
```

---

## Upgrade Recommendations

### For Development
- Use `VicidialConfig` and `VicidialClient` (modern API)
- Adopt Response DTOs for structured data access
- Enable retries with `RetryConfig::default()`

### For Production
- ✅ **Must**: Set explicit timeouts (`timeout: 60, connectTimeout: 10`)
- ✅ **Must**: Enable logging with PSR-3 logger
- ✅ **Recommended**: Enable retries (`RetryConfig::default()`)
- ✅ **Recommended**: Use HTTPS (`protocol: 'https'`)
- ✅ **Recommended**: Use Response DTOs for better error handling

### Minimal Production Configuration
```php
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use VicidialApi\VicidialConfig;
use VicidialApi\VicidialClient;
use VicidialApi\ValueObjects\RetryConfig;

$logger = new Logger('vicidial-api');
$logger->pushHandler(new StreamHandler('logs/vicidial.log', Logger::DEBUG));

$config = VicidialConfig::create(
    host: getenv('VICIDIAL_HOST'),
    user: getenv('VICIDIAL_USER'),
    pass: getenv('VICIDIAL_PASS'),
    protocol: 'https',
    timeout: 60,
    connectTimeout: 10,
    retryConfig: RetryConfig::default(),
    logger: $logger,
);

$client = VicidialClient::make($config);
```

---

## Support

- **Documentation**: See [README.md](README.md) and docs folder
- **Examples**: See [example/](example/) directory
- **Issues**: https://github.com/masterfermin02/vicidial-api-wrapper/issues
