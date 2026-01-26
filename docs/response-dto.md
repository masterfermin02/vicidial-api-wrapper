# Response DTO Guide

## Overview

The Vicidial API Wrapper now provides a comprehensive Response DTO (Data Transfer Object) system that replaces raw string responses with structured, type-safe response objects.

## Benefits

- **Structured Data Access**: No more regex or manual string parsing
- **Status Detection**: Built-in `ok()` and `failed()` methods
- **Multiple Formats**: Access response as array, text, or JSON
- **Type Safety**: Full IDE autocomplete and type checking
- **Request Tracking**: Optional request ID for debugging
- **Consistent API**: Same interface across all endpoints

## The VicidialResponse Object

### Properties

```php
$response->status();    // 'success' or 'error'
$response->raw();       // Original response string
$response->parsed();    // Parsed array of data
$response->requestId(); // Request ID (if available)
```

### Helper Methods

```php
$response->ok();        // true if successful
$response->failed();    // true if error occurred
$response->asArray();   // Get as associative array
$response->asText();    // Get as plain text
$response->toJson();    // Convert to JSON string
$response->get($key, $default); // Get specific value
$response->has($key);   // Check if key exists
```

### Array Access

VicidialResponse implements `ArrayAccess`, so you can use array syntax:

```php
if (isset($response['lead_id'])) {
    echo $response['lead_id'];
}
```

### String Conversion

VicidialResponse implements `Stringable`:

```php
echo $response; // Outputs raw response
```

## Usage Examples

### Basic Usage

```php
use VicidialApi\VicidialClient;
use VicidialApi\VicidialConfig;

$config = VicidialConfig::create(
    host: 'vicidial.example.com',
    user: 'api_user',
    pass: 'api_password'
);

$client = VicidialClient::make($config);

// Use Response DTO methods (new)
$response = $client->agent()->hangupResponse("agent123");

if ($response->ok()) {
    echo "Hangup successful!";
} else {
    echo "Hangup failed: " . $response->raw();
}
```

### Backward Compatibility

All original methods still work and return raw strings:

```php
// Old way - still works
$result = $client->agent()->hangup("agent123");
echo $result; // Raw string

// New way - returns VicidialResponse DTO
$response = $client->agent()->hangupResponse("agent123");
echo $response->raw(); // Same raw string
```

### Agent API Examples

#### Update Fields

```php
$response = $client->agent()->updateFieldsResponse("agent123", [
    'first_name' => 'John',
    'last_name' => 'Doe',
    'phone_number' => '5551234567'
]);

if ($response->ok()) {
    echo "Fields updated successfully";
    
    // Access specific values
    if ($response->has('lead_id')) {
        $leadId = $response->get('lead_id');
        echo "Lead ID: $leadId";
    }
}
```

#### External Dial

```php
$response = $client->agent()->dialResponse("agent123", [
    'phone_number' => '5551234567',
    'phone_code' => '1'
]);

if ($response->ok()) {
    // Log the entire response
    file_put_contents('dial_log.json', $response->toJson());
    
    // Parse response data
    foreach ($response->asArray() as $key => $value) {
        echo "$key: $value\n";
    }
}
```

#### Pause Agent

```php
$response = $client->agent()->pauseResponse("agent123", "PAUSE");

if ($response->ok()) {
    echo "Agent paused";
} else {
    // Handle error
    error_log("Failed to pause agent: " . $response->asText());
}
```

#### Disposition Call

```php
$response = $client->agent()->dispoResponse("agent123", [
    'value' => 'SALE',
    'vendor_lead_code' => 'LEAD123'
]);

if ($response->ok()) {
    // Extract parsed data
    $data = $response->parsed();
    $status = $data['status'] ?? 'unknown';
    echo "Disposition set: $status";
}
```

### Admin API Examples

#### Get Version

```php
$response = $client->admin()->versionResponse();

echo "Vicidial Version: " . $response->asText();
```

#### MOH List

```php
$response = $client->admin()->mohListResponse([
    'format' => 'selectframe'
]);

if ($response->ok()) {
    $mohData = $response->asArray();
    
    foreach ($mohData as $key => $value) {
        echo "MOH $key: $value\n";
    }
}
```

#### Agent Status

```php
$response = $client->admin()->agentStatusResponse([
    'agent_user' => 'agent123'
]);

if ($response->ok()) {
    $status = $response->get('status', 'unknown');
    $campaign = $response->get('campaign', 'N/A');
    
    echo "Agent Status: $status (Campaign: $campaign)";
}
```

#### Add Lead

```php
$response = $client->admin()->addLeadResponse([
    'phone_number' => '5551234567',
    'first_name' => 'Jane',
    'last_name' => 'Smith',
    'list_id' => '101'
]);

if ($response->ok()) {
    $leadId = $response->get('lead_id');
    echo "Lead added with ID: $leadId";
} else {
    echo "Failed to add lead: " . $response->raw();
}
```

### Building Reliable Logic

The Response DTO makes it easy to build reliable, production-ready workflows:

```php
// Multi-step workflow with error handling
$hangup = $client->agent()->hangupResponse("agent123");

if ($hangup->ok()) {
    // Only proceed if hangup succeeded
    $dispo = $client->agent()->dispoResponse("agent123", ['value' => 'SALE']);
    
    if ($dispo->ok()) {
        // Only pause if both succeeded
        $pause = $client->agent()->pauseResponse("agent123", "PAUSE");
        
        if ($pause->ok()) {
            echo "Workflow completed successfully";
            
            // Log everything for audit trail
            $audit = [
                'agent' => 'agent123',
                'actions' => [
                    'hangup' => $hangup->toArray(),
                    'dispo' => $dispo->toArray(),
                    'pause' => $pause->toArray()
                ],
                'timestamp' => time()
            ];
            
            file_put_contents('audit.json', json_encode($audit));
        }
    }
}
```

### Response Format Handling

VicidialResponse automatically detects and parses different response formats:

#### JSON Response
```php
// API returns: {"result":"SUCCESS","lead_id":"12345"}
$response = $client->agent()->updateFieldsResponse(...);
echo $response->get('lead_id'); // "12345"
```

#### Key-Value Pairs
```php
// API returns: result=SUCCESS&lead_id=12345
$response = $client->admin()->addLeadResponse(...);
echo $response->get('lead_id'); // "12345"
```

#### Pipe-Delimited
```php
// API returns: SUCCESS|12345|active
$response = $client->agent()->hangupResponse(...);
echo $response->get('field_0'); // "SUCCESS"
echo $response->get('field_1'); // "12345"
```

#### Multi-Line
```php
// API returns:
// result: SUCCESS
// lead_id: 12345
$response = $client->admin()->versionResponse();
echo $response->get('result'); // "SUCCESS"
```

#### Plain Text
```php
// API returns: SUCCESS
$response = $client->agent()->pauseResponse(...);
echo $response->get('result'); // "SUCCESS"
```

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
