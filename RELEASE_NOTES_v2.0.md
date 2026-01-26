# Release Notes - v2.0

## 🎉 Major Release: Configuration Object Pattern

Version 2.0 introduces a modern, type-safe configuration approach while maintaining 100% backward compatibility.

---

## ✨ New Features

### 1. VicidialConfig - Configuration Object
A clean, explicit way to configure your Vicidial API client:

```php
$config = VicidialConfig::create(
    host: 'your-server.com',
    user: 'api_user',
    pass: 'api_password',
    protocol: 'https',
    verifyTls: true,
    timeout: 60,
    urlEncodeDefault: false,
    userAgent: 'MyApp/1.0',
    source: 'myapp'
);
```

**Benefits:**
- Named parameters for clarity
- IDE autocomplete support
- Built-in validation
- Type safety
- Extensible design

### 2. VicidialClient - New Entrypoint
Simplified client creation:

```php
$client = VicidialClient::make($config);
// or
$client = VicidialApi::client($config);
```

### 3. Advanced Configuration Options
Full control over:
- **Protocol**: HTTP or HTTPS
- **TLS Verification**: Enable/disable certificate validation
- **Timeout**: Custom request timeouts
- **User Agent**: Custom user agent strings
- **URL Encoding**: Default URL encoding behavior
- **Source Tracking**: Identifier for API usage tracking

### 4. Basic Authentication Support
Enhanced remote agent authentication:

```php
$config = VicidialConfig::createWithBasicAuth(
    host: 'server.com',
    user: 'agent_user',
    pass: 'agent_pass',
    basicAuthUser: 'http_user',
    basicAuthPass: 'http_pass'
);
```

---

## 📖 Documentation

### New Documentation Files
- **MIGRATION.md**: Step-by-step migration guide from v1.x
- **QUICKSTART.md**: Quick reference for common operations
- **example/using_config.php**: Comprehensive examples

### Updated Documentation
- **README.md**: 
  - Added "What's New" section
  - Comprehensive authentication documentation
  - Separated recommended vs legacy approaches
  - Fixed duplicate examples
  - Fixed code errors
  - Clarified auth modes

---

## 🔄 Breaking Changes

**None!** This release is 100% backward compatible.

All v1.x code continues to work without modifications. Legacy methods are marked as deprecated but remain functional.

---

## 🗑️ Deprecations

The following methods are deprecated in favor of the new configuration approach:

- `VicidialApi::create()` → Use `VicidialConfig::create()` + `VicidialClient::make()`
- `VicidialApi::createWithBasicAuth()` → Use `VicidialConfig::createWithBasicAuth()` + `VicidialClient::make()`

**Note:** Deprecated methods will continue to work in v2.x and may be removed in v3.0.

---

## 🐛 Bug Fixes

- Fixed duplicate Example 3 in README (was showing updateFields twice)
- Fixed bracket mismatch in remote agent hangUp example
- Fixed namespace issue in AgentTest
- Improved authentication documentation clarity

---

## 🧪 Testing

- Added comprehensive test suite for VicidialConfig
- All 21 tests passing
- 43 assertions
- 0 errors

---

## 📦 Installation

### New Installation
```bash
composer require masterfermin02/vicidial-api-wrapper
```

### Upgrade from v1.x
```bash
composer update masterfermin02/vicidial-api-wrapper
```

**No code changes required!** But we recommend migrating to the new approach for better features.

---

## 🚀 Migration Guide

See [MIGRATION.md](MIGRATION.md) for detailed instructions.

**Quick example:**

**Before (v1.x):**
```php
$client = VicidialApi::create('server.com', 'user', 'pass');
$client->agent()->pause('user', 'PAUSE');
```

**After (v2.0):**
```php
$config = VicidialConfig::create(
    host: 'server.com',
    user: 'user',
    pass: 'pass',
    protocol: 'https'  // Now you can configure HTTPS!
);
$client = VicidialClient::make($config);
$client->agent()->pause('user', 'PAUSE');
```

---

## 🎯 Use Cases

### Development
```php
$config = VicidialConfig::create(
    host: 'dev-server.com',
    user: 'dev_user',
    pass: 'dev_pass',
    verifyTls: false,  // Disable cert verification for dev
    timeout: 120
);
```

### Production with HTTPS
```php
$config = VicidialConfig::create(
    host: 'api.vicidial.com',
    user: 'prod_user',
    pass: 'prod_pass',
    protocol: 'https',
    verifyTls: true,
    timeout: 30,
    userAgent: 'MyCompany/1.0'
);
```

### Remote Agent
```php
$config = VicidialConfig::createWithBasicAuth(
    host: 'remote.vicidial.com',
    user: 'agent123',
    pass: 'agent_pass',
    basicAuthUser: 'api_user',
    basicAuthPass: 'api_pass',
    protocol: 'https'
);
```

---

## 📊 Stats

- **Lines Added**: ~800+
- **New Classes**: 2 (VicidialConfig, VicidialClient)
- **New Tests**: 7
- **Documentation Pages**: 4 new files
- **Examples**: 6 comprehensive examples
- **Backward Compatibility**: 100%

---

## 🙏 Credits

Special thanks to all contributors and users who requested better configuration support!

---

## 🔮 What's Next?

### Planned for v2.1
- Connection pooling support
- Retry logic configuration
- Request/response logging
- PSR-3 logger integration

### Planned for v3.0
- Remove deprecated factory methods
- PHP 8.2+ requirement
- Additional validation features

---

## 📞 Support

- **Documentation**: See README.md
- **Quick Start**: See QUICKSTART.md
- **Migration**: See MIGRATION.md
- **Examples**: See example/using_config.php
- **Issues**: GitHub Issues
- **Discussions**: GitHub Discussions

---

**Enjoy the new configuration system! 🎊**
