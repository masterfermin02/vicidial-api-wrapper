# Quick Start Guide - VicidialConfig

## Installation

```bash
composer require masterfermin02/vicidial-api-wrapper
```

## Basic Usage

### 1. Standard Authentication (Most Common)

```php
<?php
require_once 'vendor/autoload.php';

use VicidialApi\VicidialConfig;
use VicidialApi\VicidialClient;

$config = VicidialConfig::create(
    host: 'your-server.com',
    user: 'your-user',
    pass: 'your-password'
);

$client = VicidialClient::make($config);

// Use the client
$client->agent()->pause('agent_user', 'PAUSE');
$client->admin()->version();
```

### 2. With HTTPS (Recommended for Production)

```php
$config = VicidialConfig::create(
    host: 'your-server.com',
    user: 'your-user',
    pass: 'your-password',
    protocol: 'https',
    timeout: 60
);

$client = VicidialClient::make($config);
```

### 3. Remote Agent with Basic Auth

```php
$config = VicidialConfig::createWithBasicAuth(
    host: 'your-server.com',
    user: 'agent-user',
    pass: 'agent-password',
    basicAuthUser: 'http-auth-user',
    basicAuthPass: 'http-auth-password',
    protocol: 'https'
);

$client = VicidialClient::make($config);
$client->remoteAgent()->active('agent_id', 'conf_exten');
```

## All Configuration Options

```php
$config = VicidialConfig::create(
    host: 'server.com',              // Required: Server hostname/IP
    user: 'username',                // Required: API username
    pass: 'password',                // Required: API password
    protocol: 'https',               // Optional: 'http' or 'https' (default: 'http')
    verifyTls: true,                 // Optional: Verify SSL certs (default: true)
    timeout: 30,                     // Optional: Timeout in seconds (default: 30)
    urlEncodeDefault: false,         // Optional: URL encode by default (default: false)
    userAgent: 'MyApp/1.0',         // Optional: Custom user agent
    source: 'myapp'                  // Optional: Source tracking (default: 'test')
);
```

## Common Operations

### Agent API

```php
$agent = $client->agent();

// Update fields
$agent->updateFields('user', ['first_name' => 'John', 'last_name' => 'Doe']);

// Make a call
$agent->dial('user', ['phone_number' => '1234567890', 'phone_code' => '1']);

// Pause/unpause
$agent->pause('user', 'PAUSE');

// Disposition
$agent->dispo('user', ['value' => 'SALE']);

// Hangup
$agent->hangup('user');

// Logout
$agent->logout('user');
```

### Admin API

```php
$admin = $client->admin();

// Get version
$version = $admin->version();

// Get MOH list
$mohList = $admin->mohList(['format' => 'selectframe']);

// Add lead
$admin->addLead([
    'phone_number' => '1234567890',
    'first_name' => 'John',
    'last_name' => 'Doe',
    'list_id' => '123'
]);
```

### Remote Agent API

```php
$remoteAgent = $client->remoteAgent();

// Activate
$remoteAgent->active('agent_id', 'conf_exten');

// Hangup with status
$remoteAgent->hangUp('agent_id', ['status' => 'SALE']);

// Deactivate
$remoteAgent->inActive('agent_id', 'conf_exten');
```

## Environment Variables

```php
// .env file
VICIDIAL_HOST=your-server.com
VICIDIAL_USER=your-user
VICIDIAL_PASS=your-password
VICIDIAL_PROTOCOL=https
VICIDIAL_TIMEOUT=60
```

```php
// PHP code
$config = VicidialConfig::create(
    host: getenv('VICIDIAL_HOST'),
    user: getenv('VICIDIAL_USER'),
    pass: getenv('VICIDIAL_PASS'),
    protocol: getenv('VICIDIAL_PROTOCOL') ?: 'http',
    timeout: (int)(getenv('VICIDIAL_TIMEOUT') ?: 30)
);
```

## Error Handling

```php
try {
    $client = VicidialClient::make($config);
    $response = $client->agent()->pause('user', 'PAUSE');
    echo "Success: $response";
} catch (\InvalidArgumentException $e) {
    echo "Configuration error: " . $e->getMessage();
} catch (\Exception $e) {
    echo "API error: " . $e->getMessage();
}
```

## Migration from v1.x

**Old way:**
```php
$client = VicidialApi::create('server.com', 'user', 'pass');
$client->agent()->pause('user', 'PAUSE');
```

**New way:**
```php
$config = VicidialConfig::create(
    host: 'server.com',
    user: 'user',
    pass: 'pass'
);
$client = VicidialClient::make($config);
$client->agent()->pause('user', 'PAUSE');
```

**Note:** The old way still works but is deprecated.

## Resources

- [Full Documentation](README.md)
- [Migration Guide](../MIGRATION.md)
- [Complete Examples](example/using_config.php)
- [Agent API Docs](agent.md)
- [Admin API Docs](admin.md)

---

**Quick tip:** Start with the basic configuration and add options as needed!
