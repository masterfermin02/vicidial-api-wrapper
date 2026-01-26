# Test Suite Summary

## Overview
Comprehensive unit and contract tests for the Vicidial API Wrapper without requiring a live Vicidial instance.

## Test Structure

### Unit Tests (`tests/Unit/`)
Tests individual components in isolation without external dependencies.

#### ValueObjects Tests
- **BaseUriTest.php** - URL building with various protocols and formats
- **QueryParamsTest.php** - Query parameter construction and encoding  
- **ParameterTest.php** - Parameter value conversion and encoding
- **HeadersTest.php** - HTTP header building (Bearer vs Basic Auth)
- **PayloadTest.php** - Request payload construction and URL building

#### Response Tests  
- **VicidialResponseTest.php** - Response parsing for all Vicidial formats

### Contract Tests (`tests/Contract/`)
Tests integration between components using mocked HTTP client.

- **HttpTransporterTest.php** - HTTP request/response flow with PSR-18 mock
- **RetryBehaviorTest.php** - Basic transport behavior with retry config

### Test Fixtures (`tests/Fixtures/`)
- **VicidialResponses.php** - Known Vicidial API response fixtures based on documentation

### Mocks (`tests/Mocks/`)
- **MockPsrClient.php** - PSR-18 HTTP client mock for deterministic testing

## Running Tests

```bash
# Run all unit tests
vendor/bin/phpunit tests/Unit --testdox

# Run response parsing tests  
vendor/bin/phpunit tests/Responses --testdox

# Run contract tests
vendor/bin/phpunit tests/Contract --testdox

# Run all new tests
vendor/bin/phpunit tests/Unit tests/Responses tests/Contract --testdox

# Run with coverage
vendor/bin/phpunit tests/Unit tests/Responses tests/Contract
```

## Test Coverage

### URL Building & Encoding
✅ Protocol handling (http/https auto-detection)
✅ Query parameter encoding
✅ Special characters in URLs
✅ Array parameter conversion
✅ Integer/string parameter handling

### Authentication
✅ Bearer token format
✅ Basic Auth encoding
✅ Header construction

### Response Parsing
✅ SUCCESS/ERROR/NOTICE detection
✅ Pipe-delimited data extraction
✅ Multi-line key-value parsing
✅ VERSION format parsing
✅ Special characters in responses

### HTTP Transport
✅ GET requests with query params
✅ POST requests with JSON body
✅ URL construction
✅ Header injection
✅ Request/response flow

## Benefits

1. **No External Dependencies** - Tests run without Vicidial server
2. **Fast Execution** - No network calls, instant feedback
3. **Deterministic** - Consistent results using fixtures
4. **Regression Prevention** - Catches breaking changes early
5. **Documentation** - Tests serve as usage examples
6. **CI/CD Ready** - Can run in any environment

## Test Fixtures

All fixtures in `VicidialResponses.php` are based on actual Vicidial API documentation and real-world responses:

- Success responses with data
- Error responses with details
- Version information
- Webserver settings
- Lead/campaign lists
- Multi-format responses

## Future Enhancements

To test full retry behavior with 5xx errors, you would need:
- Guzzle integration test (throws ClientException for HTTP errors)
- Network error simulation
- Timeout handling tests

The current test suite covers all core functionality that can be tested deterministically without external HTTP dependencies.
