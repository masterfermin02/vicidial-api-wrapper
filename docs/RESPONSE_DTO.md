# Response DTO Documentation

This document describes the Response Data Transfer Objects (DTOs) used in the VicidialApi package for handling API responses.

## Table of Contents

- [Overview](#overview)
- [VicidialResponse](#vicidialresponse)
  - [Creating Instances](#creating-instances)
  - [Properties and Methods](#properties-and-methods)
  - [Response Status](#response-status)
  - [Accessing Data](#accessing-data)
  - [Response Formats](#response-formats)
  - [Array Access](#array-access)
- [Response (Internal)](#response-internal)
- [Usage Examples](#usage-examples)
- [Error Handling](#error-handling)

## Overview

The VicidialApi package provides structured response objects that parse and normalize Vicidial API responses. The primary DTO is `VicidialResponse`, which provides:

- **Structured access** to response data
- **Helper methods** for checking success/failure
- **Automatic parsing** of various Vicidial response formats
- **Request tracking** via request IDs
- **Type safety** with proper return types

## VicidialResponse

The `VicidialResponse` class is the main DTO for handling API responses from the Vicidial API.

### Creating Instances

The `HttpTransporter` automatically creates `VicidialResponse` instances when using methods that end with `Response()`:

```php
use VicidialApi\VicidialClient;

$client = VicidialClient::make($config);

// Methods that return VicidialResponse
$response = $client->agent()->pauseResponse('agent_user', 'PAUSE');
$response = $client->agent()->dialResponse('agent_user', ['phone_number' => '1234567890']);
$response = $client->admin()->versionResponse();
```

You can also create instances manually:

```php
use VicidialApi\Responses\VicidialResponse;

$response = VicidialResponse::from($content, $headers);
```

### Properties and Methods

#### Core Methods

##### `status(): string`

Returns the response status: `'success'`, `'error'`, or `'notice'`.

```php
$status = $response->status();
// Returns: 'success' | 'error' | 'notice'
```

##### `raw(): string`

Returns the raw, unparsed response string as received from the API.

```php
$raw = $response->raw();
// Example: "SUCCESS: external_pause function set - PAUSE|1232020456|6666"
```

##### `parsed(): array`

Returns the parsed response as an associative array.

```php
$data = $response->parsed();
// Example: ['result_type' => 'SUCCESS', 'message' => 'external_pause function set - PAUSE|1232020456|6666', ...]
```

##### `requestId(): ?string`

Returns the request ID if available from response headers (X-Request-ID).

```php
$requestId = $response->requestId();
// Example: "req_abc123xyz789"
```

### Response Status

#### `ok(): bool`

Checks if the response indicates success.

```php
if ($response->ok()) {
    echo "Operation successful!";
}
```

This method returns `true` when:
- The status is `'success'`
- The raw response starts with `"SUCCESS:"`

#### `failed(): bool`

Checks if the response indicates an error.

```php
if ($response->failed()) {
    echo "Operation failed: " . $response->raw();
}
```

This is the inverse of `ok()`.

### Accessing Data

#### `get(string $key, mixed $default = null): mixed`

Retrieves a specific value from the parsed response with an optional default value.

```php
$leadId = $response->get('lead_id', '0');
$message = $response->get('message');
```

#### `has(string $key): bool`

Checks if a key exists in the parsed response.

```php
if ($response->has('lead_id')) {
    $leadId = $response->get('lead_id');
}
```

#### `asArray(): array`

Returns the parsed response as an array (alias for `parsed()`).

```php
$data = $response->asArray();
foreach ($data as $key => $value) {
    echo "$key: $value\n";
}
```

#### `asText(): string`

Returns the raw response as text (alias for `raw()`).

```php
$text = $response->asText();
echo $text;
```

#### `toJson(int $flags = 0): string`

Converts the response to a JSON string.

```php
$json = $response->toJson(JSON_PRETTY_PRINT);
// Returns: {"status":"success","raw":"SUCCESS: ...","parsed":{...},"requestId":"req_123"}
```

#### `toArray(): array`

Converts the entire response structure to an array.

```php
$array = $response->toArray();
// Returns:
// [
//     'status' => 'success',
//     'raw' => 'SUCCESS: ...',
//     'parsed' => [...],
//     'requestId' => 'req_123'
// ]
```

#### `__toString(): string`

Converts the response to a string (returns raw content).

```php
echo $response; // Outputs the raw response string
```

### Response Formats

The `VicidialResponse` class automatically parses various Vicidial API response formats:

#### 1. SUCCESS/ERROR Format

The most common format:

```
SUCCESS: external_pause function set - PAUSE|1232020456|6666
```

Parsed as:
```php
[
    'result_type' => 'SUCCESS',
    'message' => 'external_pause function set - PAUSE|1232020456|6666',
    'data_0' => 'external_pause function set - PAUSE',
    'data_1' => '1232020456',
    'data_2' => '6666'
]
```

#### 2. Version Format

```
VERSION: 2.0.5-2|BUILD: 90116-1229|DATE: 2009-01-15 14:59:33|EPOCH: 1222020803
```

Parsed as:
```php
[
    'version' => '2.0.5-2',
    'build' => '90116-1229',
    'date' => '2009-01-15 14:59:33',
    'epoch' => '1222020803'
]
```

#### 3. Pipe-Delimited Lists

```
user|allowed_campaigns|allowed_ingroups
```

Parsed as:
```php
[
    'field_0' => 'user',
    'field_1' => 'allowed_campaigns',
    'field_2' => 'allowed_ingroups'
]
```

#### 4. Key-Value Format

```
set.timezone: America/New_York
abbr.timezone: EDT
```

Parsed as:
```php
[
    'set.timezone' => 'America/New_York',
    'abbr.timezone' => 'EDT'
]
```

#### 5. JSON Format

If the response is valid JSON, it's parsed as-is:

```json
{"status": "success", "data": {"id": 123}}
```

Parsed as:
```php
[
    'status' => 'success',
    'data' => ['id' => 123]
]
```

### Array Access

`VicidialResponse` implements `ArrayAccess`, allowing you to access parsed data like an array:

```php
// Check if key exists
if (isset($response['lead_id'])) {
    // Get value
    $leadId = $response['lead_id'];
}

// Note: Setting and unsetting are not allowed (immutable)
$response['key'] = 'value'; // Throws BadMethodCallException
unset($response['key']);     // Throws BadMethodCallException
```

## Response (Internal)

The `Response` class is an internal value object used by the transporter for generic responses.

### Usage

This class is primarily used internally but is returned by lower-level transporter methods:

```php
use VicidialApi\ValueObjects\Transporter\Response;

$response = Response::from($data);
$data = $response->data(); // Returns array|string
```

### Methods

##### `from(array|string $data): Response`

Creates a new Response instance from array or string data.

##### `data(): array|string`

Returns the response data.

## Usage Examples

### Example 1: Basic Success Check

```php
$response = $client->agent()->pauseResponse('agent_user', 'PAUSE');

if ($response->ok()) {
    echo "Agent paused successfully\n";
    echo "Status: " . $response->status() . "\n";
} else {
    echo "Failed to pause agent: " . $response->raw() . "\n";
}
```

### Example 2: Accessing Parsed Data

```php
$response = $client->agent()->updateFieldsResponse('agent_user', [
    'first_name' => 'John',
    'last_name' => 'Doe',
    'address1' => '123 Main St'
]);

if ($response->has('lead_id')) {
    $leadId = $response->get('lead_id');
    echo "Updated lead ID: $leadId\n";
}

// Or use array access
if (isset($response['lead_id'])) {
    echo "Lead ID: " . $response['lead_id'] . "\n";
}
```

### Example 3: Version Information

```php
$response = $client->admin()->versionResponse();

echo "Vicidial Version: " . $response->asText() . "\n";

// Access specific version components
if ($response->has('version')) {
    echo "Version: " . $response->get('version') . "\n";
}
if ($response->has('build')) {
    echo "Build: " . $response->get('build') . "\n";
}
```

### Example 4: Chaining Operations

```php
// Hangup call, then set disposition
$hangupResponse = $client->agent()->hangupResponse('agent_user');

if ($hangupResponse->ok()) {
    echo "Call hung up successfully\n";
    
    $dispoResponse = $client->agent()->dispoResponse('agent_user', [
        'value' => 'SALE'
    ]);
    
    if ($dispoResponse->ok()) {
        echo "Disposition set to SALE\n";
    }
}
```

### Example 5: Logging and Analytics

```php
$response = $client->agent()->dialResponse('agent_user', [
    'phone_number' => '1234567890',
    'phone_code' => '1'
]);

// Log the entire response for debugging
file_put_contents('api_log.json', $response->toJson(JSON_PRETTY_PRINT), FILE_APPEND);

// Or create structured logs
$logData = [
    'timestamp' => date('Y-m-d H:i:s'),
    'action' => 'dial',
    'agent' => 'agent_user',
    'status' => $response->status(),
    'success' => $response->ok(),
    'request_id' => $response->requestId(),
    'raw_response' => $response->raw()
];

error_log(json_encode($logData));
```

### Example 6: Error Recovery

```php
$maxRetries = 3;
$attempt = 0;
$response = null;

while ($attempt < $maxRetries) {
    $response = $client->agent()->pauseResponse('agent_user', 'PAUSE');
    
    if ($response->ok()) {
        echo "Agent paused successfully on attempt " . ($attempt + 1) . "\n";
        break;
    }
    
    $attempt++;
    
    if ($attempt < $maxRetries) {
        echo "Attempt $attempt failed, retrying...\n";
        sleep(1); // Wait before retry
    } else {
        echo "All attempts failed: " . $response->raw() . "\n";
    }
}
```

### Example 7: Building API Wrappers

```php
class MyVicidialService
{
    public function __construct(private VicidialClient $client)
    {
    }
    
    public function pauseAgent(string $agentUser, string $pauseCode): array
    {
        $response = $this->client->agent()->pauseResponse($agentUser, $pauseCode);
        
        return [
            'success' => $response->ok(),
            'status' => $response->status(),
            'message' => $response->raw(),
            'data' => $response->parsed(),
            'request_id' => $response->requestId()
        ];
    }
    
    public function dialNumber(string $agentUser, string $phoneNumber): bool
    {
        $response = $this->client->agent()->dialResponse($agentUser, [
            'phone_number' => $phoneNumber,
            'phone_code' => '1'
        ]);
        
        return $response->ok();
    }
}
```

## Error Handling

### HTTP Errors

When the API returns an HTTP error (4xx/5xx status codes) with a JSON error structure, the transporter throws an `ErrorException`:

```php
use VicidialApi\Exceptions\ErrorException;

try {
    $response = $client->agent()->pauseResponse('invalid_agent', 'PAUSE');
} catch (ErrorException $e) {
    echo "API Error: " . $e->getMessage() . "\n";
    echo "Error Type: " . $e->getErrorType() . "\n";
    echo "Error Code: " . $e->getErrorCode() . "\n";
}
```

Expected error JSON structure:
```json
{
  "error": {
    "message": "agent_user is not logged in",
    "type": "authentication_error",
    "code": "AGENT_NOT_LOGGED_IN"
  }
}
```

### Response-Level Errors

Some errors are returned with HTTP 200 but contain "ERROR:" in the response:

```php
$response = $client->agent()->pauseResponse('agent_user', 'PAUSE');

if ($response->failed()) {
    // Response status is 'error'
    echo "Operation failed: " . $response->raw() . "\n";
    // Example: "ERROR: agent_user is not logged in - 6666"
    
    // Access error details from parsed response
    if ($response->has('message')) {
        echo "Error message: " . $response->get('message') . "\n";
    }
}
```

### Transport Errors

Network errors and connection issues throw `TransporterException`:

```php
use VicidialApi\Exceptions\TransporterException;

try {
    $response = $client->agent()->pauseResponse('agent_user', 'PAUSE');
} catch (TransporterException $e) {
    echo "Transport Error: " . $e->getMessage() . "\n";
    // Handle network errors, timeouts, etc.
}
```

### Unserializable Responses

If the response cannot be parsed, an `UnserializableResponse` exception is thrown:

```php
use VicidialApi\Exceptions\UnserializableResponse;

try {
    $response = $client->agent()->someMethod();
} catch (UnserializableResponse $e) {
    echo "Invalid Response: " . $e->getMessage() . "\n";
}
```

## Best Practices

1. **Always check response status** before processing data:
   ```php
   if ($response->ok()) {
       // Process successful response
   } else {
       // Handle error
   }
   ```

2. **Use `has()` before `get()`** to avoid null values:
   ```php
   if ($response->has('lead_id')) {
       $leadId = $response->get('lead_id');
   }
   ```

3. **Provide default values** when accessing optional fields:
   ```php
   $value = $response->get('optional_field', 'default_value');
   ```

4. **Log request IDs** for debugging and tracking:
   ```php
   if ($requestId = $response->requestId()) {
       error_log("Request ID: $requestId");
   }
   ```

5. **Use structured logging** with `toJson()` or `toArray()`:
   ```php
   file_put_contents('api.log', $response->toJson() . "\n", FILE_APPEND);
   ```

6. **Handle exceptions** at appropriate levels:
   ```php
   try {
       $response = $client->agent()->pauseResponse('agent', 'PAUSE');
   } catch (ErrorException $e) {
       // Handle API errors
   } catch (TransporterException $e) {
       // Handle network errors
   }
   ```

## See Also

- [Response Formats](response-formats.md) - Detailed information about Vicidial response formats
- [Agent API Documentation](agent.md) - Agent API methods
- [Admin API Documentation](admin.md) - Admin API methods
- [Reliability Features](RELIABILITY_FEATURES.md) - Retry mechanisms and error handling

---

# Appendix: naming, method list, testing and migration

_Merged from the former `docs/response-dto.md`. The method list below is the v4.0 snapshot; every resource method now has a `...Response()` variant for the Admin API, see `docs/admin.md` and `docs/agent.md` for the full list._

## Response Method Naming

All new Response DTO methods follow a consistent naming pattern:

- Original method: `methodName()` → Returns `string`
- Response DTO method: `methodNameResponse()` → Returns `VicidialResponse`

Examples:
- `hangup()` → `hangupResponse()`
- `dial()` → `dialResponse()`
- `updateFields()` → `updateFieldsResponse()`
- `mohList()` → `mohListResponse()`

## Available Response Methods

### Agent API

- `hangupResponse()`
- `dispoResponse()`
- `pauseResponse()`
- `dialResponse()`
- `updateFieldsResponse()`
- And more...

### Admin API

- `versionResponse()`
- `mohListResponse()`
- `vmListResponse()`
- `agentStatusResponse()`
- `addLeadResponse()`
- `updateLeadResponse()`
- `campaignsListResponse()`
- `hopperListResponse()`
- And more...

## Error Handling

```php
try {
    $response = $client->agent()->dialResponse("agent123", [
        'phone_number' => '5551234567'
    ]);
    
    if ($response->ok()) {
        // Success
        echo "Call initiated";
    } else {
        // API returned error
        error_log("API Error: " . $response->raw());
        echo "Failed to initiate call";
    }
} catch (Exception $e) {
    // Network or configuration error
    error_log("Exception: " . $e->getMessage());
    echo "System error occurred";
}
```

## Testing with Response DTOs

```php
use VicidialApi\Responses\VicidialResponse;

// Create mock responses for testing
$successResponse = VicidialResponse::from('SUCCESS');
$this->assertTrue($successResponse->ok());

$errorResponse = VicidialResponse::from('ERROR: Invalid user');
$this->assertTrue($errorResponse->failed());

$jsonResponse = VicidialResponse::from(json_encode([
    'result' => 'SUCCESS',
    'lead_id' => '12345'
]));
$this->assertEquals('12345', $jsonResponse->get('lead_id'));
```

## Migration Guide

If you're upgrading from raw string responses:

### Before (v1.x)
```php
$result = $client->agent()->hangup("agent123");

// Manual parsing required
if (strpos($result, 'SUCCESS') !== false) {
    echo "Success";
}

// Regex for extracting data
preg_match('/lead_id=(\d+)/', $result, $matches);
$leadId = $matches[1] ?? null;
```

### After (v2.0+)
```php
$response = $client->agent()->hangupResponse("agent123");

// Built-in status check
if ($response->ok()) {
    echo "Success";
}

// Direct data access
$leadId = $response->get('lead_id');
```

## Best Practices

1. **Use Response Methods**: Prefer `methodResponse()` over `method()` for new code
2. **Check Success**: Always use `ok()` or `failed()` before processing data
3. **Handle Errors**: Implement proper error handling with try-catch and status checks
4. **Log Responses**: Use `toJson()` for structured logging
5. **Type Hints**: Type hint `VicidialResponse` in your code for better IDE support
6. **Default Values**: Use `get($key, $default)` to avoid null checks

```php
use VicidialApi\Responses\VicidialResponse;

function processAgentAction(string $agent): VicidialResponse
{
    $response = $this->client->agent()->hangupResponse($agent);
    
    if ($response->ok()) {
        $this->logger->info('Agent hangup', $response->toArray());
    } else {
        $this->logger->error('Agent hangup failed', [
            'agent' => $agent,
            'error' => $response->raw()
        ]);
    }
    
    return $response;
}
```

## See Also

- [Agent API Documentation](agent.md)
- [Admin API Documentation](admin.md)
- [Example: response_dto_usage.php](../example/response_dto_usage.php)
