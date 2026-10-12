# Migration Guide to v2.0

## Overview

Version 2.0 introduces a new configuration-based approach using `VicidialConfig` and `VicidialClient`. The legacy factory methods are still supported but deprecated.

## Key Changes

### Before (v1.x - Legacy)

```php
// Standard authentication
$client = VicidialApi::create(
    'vicidial-server.com',
    'api_user',
    'api_password'
);

// Basic authentication for remote agents
$client = VicidialApi::createWithBasicAuth(
    'vicidial-server.com',
    'api_user',
    'api_password',
    'basic_auth_user',
    'basic_auth_password'
);
```

### After (v2.0 - Recommended)

```php
use VicidialApi\VicidialConfig;
use VicidialApi\VicidialClient;

// Standard authentication
$config = VicidialConfig::create(
    host: 'vicidial-server.com',
    user: 'api_user',
    pass: 'api_password'
);
$client = VicidialClient::make($config);

// Basic authentication for remote agents
$config = VicidialConfig::createWithBasicAuth(
    host: 'vicidial-server.com',
    user: 'api_user',
    pass: 'api_password',
    basicAuthUser: 'basic_auth_user',
    basicAuthPass: 'basic_auth_password'
);
$client = VicidialClient::make($config);
```

## Benefits of the New Approach

1. **Explicit Configuration**: All options are clearly named using named parameters
2. **Type Safety**: Better IDE autocomplete and type checking
3. **Extensibility**: Easy to add new configuration options without breaking changes
4. **Better Defaults**: Sensible defaults for all optional parameters
5. **Validation**: Built-in validation for protocol, timeout, etc.
6. **Testability**: Easier to mock and test configuration objects

## Available Configuration Options

### VicidialConfig::create()

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `host` | string | *required* | Vicidial server hostname/IP |
| `user` | string | *required* | API username |
| `pass` | string | *required* | API password |
| `protocol` | string | `'http'` | Protocol: 'http' or 'https' |
| `verifyTls` | bool | `true` | Verify SSL/TLS certificates |
| `timeout` | int | `30` | Request timeout in seconds |
| `urlEncodeDefault` | bool | `false` | Deprecated no-op; the transport always URL-encodes values exactly once |
| `userAgent` | string | `'VicidialApi-PHP-Wrapper/4.1'` | Custom user agent string |
| `source` | string | `'vicidial-api-wrapper'` | Source identifier shown in VICIdial logs (max 20 chars; was `'test'` before 4.2.0) |

### VicidialConfig::createWithBasicAuth()

All parameters from `create()` plus:

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `basicAuthUser` | string | *required* | HTTP Basic Auth username |
| `basicAuthPass` | string | *required* | HTTP Basic Auth password |

## Migration Examples

### Example 1: Simple Agent API Usage

**Before:**
```php
$agentApi = VicidialApi::create(
    getenv('VICIDIAL_IP'),
    getenv('VICIDIAL_USER'),
    getenv('VICIDIAL_PASSWORD')
)->agent();

$agentApi->pause("gabriel", "PAUSE");
```

**After:**
```php
$config = VicidialConfig::create(
    host: getenv('VICIDIAL_IP'),
    user: getenv('VICIDIAL_USER'),
    pass: getenv('VICIDIAL_PASSWORD')
);

$client = VicidialClient::make($config);
$client->agent()->pause("gabriel", "PAUSE");
```

### Example 2: Admin API with Custom Settings

**Before:**
```php
// Limited configuration options with factory
$adminApi = VicidialApi::create(
    'vicidial-server.com',
    'admin_user',
    'admin_pass'
)->admin();
```

**After:**
```php
// Full control over configuration
$config = VicidialConfig::create(
    host: 'vicidial-server.com',
    user: 'admin_user',
    pass: 'admin_pass',
    protocol: 'https',
    verifyTls: true,
    timeout: 60,
    userAgent: 'MyApp/1.0'
);

$client = VicidialClient::make($config);
$adminApi = $client->admin();
```

### Example 3: Remote Agent with Basic Auth

**Before:**
```php
$remoteAgent = VicidialApi::createWithBasicAuth(
    getenv('VICIDIAL_IP'),
    getenv('VICIDIAL_API_USER'),
    getenv('VICIDIAL_API_PASSWORD'),
    getenv('REMOTE_AGENT'),
    getenv('REMOTE_PASSWORD')
)->remoteAgent();
```

**After:**
```php
$config = VicidialConfig::createWithBasicAuth(
    host: getenv('VICIDIAL_IP'),
    user: getenv('VICIDIAL_USER'),
    pass: getenv('VICIDIAL_PASSWORD'),
    basicAuthUser: getenv('VICIDIAL_API_USER'),
    basicAuthPass: getenv('VICIDIAL_API_PASSWORD'),
    protocol: 'https'
);

$client = VicidialClient::make($config);
$remoteAgent = $client->remoteAgent();
```

## Backward Compatibility

**All legacy methods continue to work** in v2.0. You can migrate at your own pace:

- `VicidialApi::create()` - Still functional
- `VicidialApi::createWithBasicAuth()` - Still functional
- `VicidialApi::factory()` - Still functional

However, these methods are marked as deprecated and may be removed in v3.0.

## Step-by-Step Migration

1. **Install/Update the package:**
   ```bash
   composer require masterfermin02/vicidial-api-wrapper
   ```

2. **Import new classes:**
   ```php
   use VicidialApi\VicidialConfig;
   use VicidialApi\VicidialClient;
   ```

3. **Replace factory calls with configuration:**
   - Change `VicidialApi::create()` to `VicidialConfig::create()`
   - Change client instantiation to `VicidialClient::make($config)`

4. **Add optional configuration parameters as needed:**
   - Set `protocol: 'https'` for secure connections
   - Adjust `timeout` for slow connections
   - Add custom `userAgent` for tracking

5. **Test thoroughly:**
   - Verify all API calls still work
   - Check error handling
   - Validate authentication

## Testing Your Migration

```php
<?php

require_once 'vendor/autoload.php';

use VicidialApi\VicidialConfig;
use VicidialApi\VicidialClient;

// Create test configuration
$config = VicidialConfig::create(
    host: 'your-test-server.com',
    user: 'test_user',
    pass: 'test_pass',
    timeout: 10
);

$client = VicidialClient::make($config);

try {
    // Test agent API
    $version = $client->agent()->version();
    echo "Agent API works: $version\n";
    
    // Test admin API
    $webserver = $client->admin()->webserver();
    echo "Admin API works: $webserver\n";
    
    echo "Migration successful!\n";
} catch (Exception $e) {
    echo "Migration issue: " . $e->getMessage() . "\n";
}
```

## Getting Help

- Check the [README](README.md) for updated examples
- Review the [example/using_config.php](example/using_config.php) file
- See [Agent API documentation](docs/agent.md)
- See [Admin API documentation](docs/admin.md)

## Timeline

- **v2.0**: New configuration approach introduced, legacy methods deprecated
- **v2.x**: Both approaches supported
- **v3.0** (future): Legacy methods may be removed

We recommend migrating to the new approach as soon as possible to take advantage of the improved features and ensure future compatibility.
