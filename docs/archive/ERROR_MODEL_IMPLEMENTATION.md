# Error Model Implementation Summary

## Overview
Implemented a comprehensive, structured exception hierarchy for the Vicidial API Wrapper that makes error handling predictable, type-safe, and developer-friendly.

## Exception Hierarchy

```
VicidialException (base class)
├── AuthenticationException
├── TransportException (wraps PSR-18 exceptions)
│   └── TransporterException (deprecated alias for backward compatibility)
├── ApiErrorException
├── InvalidResponseException
│   └── UnserializableResponse (deprecated)
├── ConfigurationException
├── RetryException
├── InvalidArgumentException (deprecated)
└── ErrorException (deprecated)
```

## New Exception Classes

### 1. VicidialException (Base)
**File**: `src/Exceptions/VicidialException.php`

- Base exception for all wrapper errors
- Catch this to handle any error from the wrapper
- Standard Exception constructor with message, code, and previous exception

### 2. AuthenticationException
**File**: `src/Exceptions/AuthenticationException.php`

- Thrown when authentication fails
- Invalid credentials, missing API user, etc.

### 3. TransportException
**File**: `src/Exceptions/TransporterException.php` (renamed from TransporterException)

- Network errors, connection timeouts, DNS failures
- Wraps PSR-18 ClientExceptionInterface
- Original PSR-18 exception available via `getPrevious()`
- Backward compatible alias: `TransporterException`

### 4. ApiErrorException
**File**: `src/Exceptions/ApiErrorException.php`

- Vicidial API returned an error response (e.g., "ERROR: invalid user")
- Provides `getRawResponse()` method to access raw API response

### 5. InvalidResponseException
**File**: `src/Exceptions/InvalidResponseException.php`

- Unexpected response format, missing fields, parse errors
- Provides `getRawResponse()` method to access raw response

### 6. ConfigurationException
**File**: `src/Exceptions/ConfigurationException.php`

- Configuration errors (invalid host, timeout, protocol, etc.)
- Replaces generic `InvalidArgumentException` usage

### 7. RetryException
**File**: `src/Exceptions/RetryException.php`

- All retry attempts exhausted
- Provides `getAttemptsMade()` method to get attempt count

## Updated Existing Exceptions

### ErrorException
- Now extends `VicidialException` instead of base `Exception`
- Marked as `@deprecated` - use `ApiErrorException` instead
- Will be removed in v3.0

### UnserializableResponse
- Now extends `InvalidResponseException` instead of base `Exception`
- Marked as `@deprecated` - use `InvalidResponseException` instead
- Will be removed in v3.0

### InvalidArgumentException
- Now extends `VicidialException` instead of base `Exception`
- Marked as `@deprecated` - use `ConfigurationException` instead
- Will be removed in v3.0

## Code Updates

### VicidialConfig.php
- Updated to throw `ConfigurationException` instead of generic `\InvalidArgumentException`
- Updated to throw `ConfigurationException` instead of generic `\RuntimeException`

### HttpTransporter.php
- Added import for `RetryException` and `TransportException`
- Updated to throw `RetryException` when all retry attempts are exhausted
- Includes attempt count in the exception
- Chains the underlying `TransportException` as previous exception

### ValueObjects/RetryConfig.php
- Updated to throw `ConfigurationException` instead of `\InvalidArgumentException`

### ValueObjects/Parameter.php
- Updated to throw `ConfigurationException` instead of `\InvalidArgumentException`

### Factory.php
- Updated to throw `ConfigurationException` for stream handler errors

## Test Updates

### tests/ValueObjects/RetryConfigTest.php
- Updated to expect `ConfigurationException` instead of `InvalidArgumentException`
- All 4 validation tests now pass

### tests/VicidialConfigTest.php
- Updated to expect `ConfigurationException` instead of `InvalidArgumentException`
- All 2 validation tests now pass

## Documentation

### docs/ERROR_HANDLING.md (NEW)
Comprehensive error handling guide including:
- Complete exception hierarchy with descriptions
- Usage examples for each exception type
- Error handling patterns (catch all, specific handling, production-ready)
- Best practices
- Migration guide from v1.x

### example/error_handling.php (NEW)
Five practical examples demonstrating:
1. Simple error handling - catch all errors
2. Specific error handling for different error types
3. Configuration error handling
4. Retry handling with automatic retries
5. Production-ready error handling with logging

### README.md
Added comprehensive "Error Handling" section with:
- Quick start example (catch all errors)
- Exception types list with descriptions
- Specific error handling example
- Production error handling example
- Link to full Error Handling Guide

## Backward Compatibility

All existing code continues to work:
- Old exception classes still exist (marked as deprecated)
- `TransporterException` is now an alias for `TransportException`
- Tests updated to use new exception types
- All 65 tests pass

## Breaking Changes (Future v3.0)

The following deprecated exceptions will be removed in v3.0:
- `ErrorException` → Use `ApiErrorException`
- `UnserializableResponse` → Use `InvalidResponseException`
- `InvalidArgumentException` → Use `ConfigurationException`
- `TransporterException` → Use `TransportException`

## Benefits

1. **Type-safe error handling**: Specific exception types for different failure scenarios
2. **Better debugging**: Raw responses and attempt counts available in exceptions
3. **Clear error hierarchy**: All wrapper errors extend `VicidialException`
4. **Production-ready**: Structured exceptions integrate well with logging and monitoring
5. **Developer-friendly**: Clear documentation and examples
6. **IDE support**: Full autocomplete and type hints
7. **Backward compatible**: Existing code continues to work

## Test Results

```
OK (65 tests, 187 assertions)
```

All tests pass successfully, including:
- 4 RetryConfig validation tests
- 2 VicidialConfig validation tests
- All response parsing tests
- All integration tests

## Files Created

1. `src/Exceptions/VicidialException.php`
2. `src/Exceptions/AuthenticationException.php`
3. `src/Exceptions/ApiErrorException.php`
4. `src/Exceptions/InvalidResponseException.php`
5. `src/Exceptions/ConfigurationException.php`
6. `src/Exceptions/RetryException.php`
7. `docs/ERROR_HANDLING.md`
8. `example/error_handling.php`

## Files Modified

1. `src/Exceptions/TransporterException.php` (now TransportException)
2. `src/Exceptions/ErrorException.php`
3. `src/Exceptions/UnserializableResponse.php`
4. `src/Exceptions/InvalidArgumentException.php`
5. `src/VicidialConfig.php`
6. `src/Transporters/HttpTransporter.php`
7. `src/ValueObjects/RetryConfig.php`
8. `src/ValueObjects/Parameter.php`
9. `src/Factory.php`
10. `tests/ValueObjects/RetryConfigTest.php`
11. `tests/VicidialConfigTest.php`
12. `README.md`

## Next Steps

Consider these future enhancements:
1. Add specific exception types for rate limiting (`RateLimitException`)
2. Add `ValidationException` for input validation errors
3. Add exception codes for programmatic error handling
4. Add more context to exceptions (request URL, parameters, etc.)
5. Create exception factory methods for common scenarios
