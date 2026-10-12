# Error Handling Guide

The Vicidial API Wrapper uses a structured exception hierarchy to help you handle different types of failures effectively. All exceptions extend from the base `VicidialException` class, allowing you to catch all wrapper errors with a single catch block or handle specific error types individually.

## Exception Hierarchy

```
VicidialException (base class - catch this for all wrapper errors)
├── AuthenticationException (authentication failures)
├── TransportException (network/HTTP transport errors)
├── ApiErrorException (Vicidial API returned an error)
├── InvalidResponseException (unexpected response format)
│   └── UnserializableResponse (JSON parsing errors) [deprecated]
├── ConfigurationException (configuration errors)
├── RetryException (retry attempts exhausted)
├── InvalidArgumentException [deprecated - use ConfigurationException]
└── ErrorException [deprecated - use ApiErrorException]
```

## Exception Types

### VicidialException
**Base exception for all wrapper errors.**

The root exception class. Catch this to handle any error from the wrapper.

```php
use VicidialApi\Exceptions\VicidialException;

try {
    $response = $client->agent()->updateFieldsResponse("gabriel", $fields);
} catch (VicidialException $e) {
    // Handles all wrapper errors
    echo "Error: " . $e->getMessage();
}
```

### AuthenticationException
**Authentication failed.**

Thrown when API authentication fails due to:
- Invalid credentials (wrong user or password)
- Missing API user configuration
- Invalid authentication configuration
- Unauthorized access

```php
use VicidialApi\Exceptions\AuthenticationException;

try {
    $response = $client->agent()->dialResponse("gabriel", ['phone_number' => '1234567890']);
} catch (AuthenticationException $e) {
    // Invalid credentials or API user
    echo "Authentication failed: " . $e->getMessage();
    // Log and notify admin, check credentials
}
```

### TransportException
**Network or HTTP transport errors.**

Thrown when there are connection issues:
- Network errors (DNS failure, host unreachable)
- Connection timeout
- SSL/TLS errors
- Any PSR-18 client exception

The original PSR-18 exception is available via `getPrevious()`.

```php
use VicidialApi\Exceptions\TransportException;

try {
    $response = $client->agent()->dialResponse("gabriel", ['phone_number' => '1234567890']);
} catch (TransportException $e) {
    // Network error, connection timeout, DNS failure
    echo "Network error: " . $e->getMessage();
    
    // Access the underlying PSR-18 exception
    if ($previous = $e->getPrevious()) {
        echo "Underlying error: " . $previous->getMessage();
    }
}
```

### ApiErrorException
**Vicidial API returned an error response.**

Thrown when the Vicidial API returns an error message, such as:
- `"ERROR: invalid user"`
- `"ERROR: no agent_user"`
- `"ERROR: Invalid phone number"`
- Any response starting with "ERROR:"

Provides access to the raw response via `getRawResponse()`.

```php
use VicidialApi\Exceptions\ApiErrorException;

try {
    $response = $client->agent()->dialResponse("gabriel", ['phone_number' => '1234567890']);
} catch (ApiErrorException $e) {
    // Vicidial returned an error response
    echo "API error: " . $e->getMessage();
    echo "Raw response: " . $e->getRawResponse();
    
    // Log for debugging, check API parameters
}
```

### InvalidResponseException
**Unexpected response format.**

Thrown when the API response cannot be parsed or has an unexpected structure:
- Missing expected fields
- Unexpected format (not JSON, not key-value pairs)
- Corrupted response data
- Empty response when data was expected

Provides access to the raw response via `getRawResponse()`.

```php
use VicidialApi\Exceptions\InvalidResponseException;

try {
    $response = $client->agent()->dialResponse("gabriel", ['phone_number' => '1234567890']);
} catch (InvalidResponseException $e) {
    // Unexpected response format
    echo "Invalid response: " . $e->getMessage();
    echo "Raw response: " . $e->getRawResponse();
    
    // Log for debugging, possible API version mismatch
}
```

### ConfigurationException
**Configuration error.**

Thrown when there are configuration issues:
- Invalid host URL
- Missing required parameters
- Invalid timeout values
- Invalid protocol (must be 'http' or 'https')
- Connect timeout greater than request timeout

```php
use VicidialApi\Exceptions\ConfigurationException;
use VicidialApi\VicidialConfig;

try {
    $config = VicidialConfig::create(
        host: '',  // Invalid empty host
        user: 'user',
        pass: 'pass'
    );
} catch (ConfigurationException $e) {
    echo "Configuration error: " . $e->getMessage();
    // Fix configuration before proceeding
}
```

### RetryException
**Retry attempts exhausted.**

Thrown when all retry attempts have been exhausted and the request still failed. This happens when automatic retries are enabled and all attempts fail.

Provides the number of attempts made via `getAttemptsMade()`.

```php
use VicidialApi\Exceptions\RetryException;
use VicidialApi\ValueObjects\RetryConfig;

$config = VicidialConfig::create(
    host: 'vicidial.example.com',
    user: 'api_user',
    pass: 'api_password',
    retryConfig: RetryConfig::default()  // Enable retries
);

$client = VicidialClient::make($config);

try {
    $response = $client->agent()->dialResponse("gabriel", ['phone_number' => '1234567890']);
} catch (RetryException $e) {
    // All retry attempts failed
    echo "Operation failed after " . $e->getAttemptsMade() . " attempts";
    echo "Error: " . $e->getMessage();
    
    // Service may be down, escalate or queue for later
}
```

## Error Handling Patterns

### Pattern 1: Catch All Errors

Simplest approach - catch all wrapper errors:

```php
use VicidialApi\Exceptions\VicidialException;

try {
    $response = $client->agent()->dialResponse("gabriel", ['phone_number' => '1234567890']);
    
    if ($response->ok()) {
        echo "Success! Lead ID: " . $response->get('lead_id');
    }
} catch (VicidialException $e) {
    // Handles all wrapper errors
    echo "Error: " . $e->getMessage();
    
    // Log error
    error_log("Vicidial API Error: " . $e->getMessage());
}
```

### Pattern 2: Handle Specific Errors

Handle different error types with specific logic:

```php
use VicidialApi\Exceptions\VicidialException;
use VicidialApi\Exceptions\AuthenticationException;
use VicidialApi\Exceptions\TransportException;
use VicidialApi\Exceptions\ApiErrorException;
use VicidialApi\Exceptions\RetryException;

try {
    $response = $client->agent()->updateFieldsResponse("gabriel", $fields);
    
    if ($response->ok()) {
        processLead($response->asArray());
    }
} catch (AuthenticationException $e) {
    // Invalid credentials - notify admin
    notifyAdmin("API credentials are invalid: " . $e->getMessage());
    
} catch (TransportException $e) {
    // Network issue - retry later or use fallback
    queueForRetry($fields);
    error_log("Network error: " . $e->getMessage());
    
} catch (ApiErrorException $e) {
    // API returned error - log and alert
    error_log("API Error: " . $e->getMessage() . " | Raw: " . $e->getRawResponse());
    
} catch (RetryException $e) {
    // All retries failed - escalate
    escalateToSupport("Failed after " . $e->getAttemptsMade() . " attempts");
    
} catch (VicidialException $e) {
    // Catch any other wrapper error
    error_log("Unexpected Vicidial error: " . $e->getMessage());
}
```

### Pattern 3: Production Error Handling

Complete production-ready error handling with logging:

```php
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use VicidialApi\Exceptions\VicidialException;
use VicidialApi\Exceptions\AuthenticationException;
use VicidialApi\Exceptions\TransportException;
use VicidialApi\Exceptions\ApiErrorException;
use VicidialApi\Exceptions\InvalidResponseException;
use VicidialApi\Exceptions\RetryException;

// Setup logger
$logger = new Logger('vicidial-api');
$logger->pushHandler(new StreamHandler('logs/vicidial.log', Logger::DEBUG));

try {
    $response = $client->agent()->updateFieldsResponse("gabriel", $fields);
    
    if ($response->ok()) {
        $logger->info('Lead updated successfully', [
            'lead_id' => $response->get('lead_id'),
            'agent' => 'gabriel'
        ]);
        
        return $response->asArray();
    } else {
        $logger->warning('Lead update returned non-success', [
            'response' => $response->asText()
        ]);
    }
    
} catch (AuthenticationException $e) {
    $logger->critical('Authentication failed', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
    
    // Send alert to admin
    sendAlert('Vicidial API authentication failed - check credentials');
    throw $e;
    
} catch (TransportException $e) {
    $logger->error('Network error', [
        'error' => $e->getMessage(),
        'previous' => $e->getPrevious()?->getMessage()
    ]);
    
    // Queue for retry with exponential backoff
    queueJobForRetry($fields, calculateBackoff());
    
} catch (ApiErrorException $e) {
    $logger->error('API error', [
        'error' => $e->getMessage(),
        'raw_response' => $e->getRawResponse(),
        'agent' => 'gabriel'
    ]);
    
    // Handle specific API errors
    if (str_contains($e->getMessage(), 'no agent_user')) {
        handleMissingAgent('gabriel');
    }
    
} catch (InvalidResponseException $e) {
    $logger->error('Invalid response format', [
        'error' => $e->getMessage(),
        'raw_response' => $e->getRawResponse()
    ]);
    
    // Possible API version mismatch
    sendAlert('Unexpected response format from Vicidial API - check compatibility');
    
} catch (RetryException $e) {
    $logger->error('All retry attempts exhausted', [
        'attempts' => $e->getAttemptsMade(),
        'error' => $e->getMessage()
    ]);
    
    // Escalate to support
    createSupportTicket('Vicidial API unavailable after ' . $e->getAttemptsMade() . ' attempts');
    
} catch (VicidialException $e) {
    $logger->error('Unexpected Vicidial error', [
        'error' => $e->getMessage(),
        'type' => get_class($e),
        'trace' => $e->getTraceAsString()
    ]);
    
    throw $e;
}
```

### Pattern 4: Async/Queue Error Handling

Error handling when processing API calls in background jobs:

```php
use VicidialApi\Exceptions\VicidialException;
use VicidialApi\Exceptions\TransportException;
use VicidialApi\Exceptions\RetryException;

class ProcessLeadJob
{
    public function handle($leadData)
    {
        try {
            $response = $this->client->agent()->updateFieldsResponse(
                $leadData['agent'],
                $leadData['fields']
            );
            
            if ($response->ok()) {
                $this->markComplete($leadData);
            } else {
                $this->markFailed($leadData, $response->asText());
            }
            
        } catch (TransportException | RetryException $e) {
            // Transient errors - retry the job
            $this->release(300); // Retry in 5 minutes
            
        } catch (VicidialException $e) {
            // Permanent error - don't retry
            $this->fail($e);
            $this->markFailed($leadData, $e->getMessage());
        }
    }
}
```

## Best Practices

### 1. Always Catch VicidialException
At minimum, catch the base `VicidialException` to handle all wrapper errors:

```php
try {
    $response = $client->agent()->dialResponse(...);
} catch (VicidialException $e) {
    // Handle error
}
```

### 2. Handle Transient vs Permanent Errors
Some errors are transient (network issues) and can be retried, others are permanent (authentication failures):

```php
try {
    // API call
} catch (TransportException | RetryException $e) {
    // Transient - retry later
    queueForRetry();
} catch (AuthenticationException | ConfigurationException $e) {
    // Permanent - fix config
    notifyAdmin();
}
```

### 3. Log Raw Responses for Debugging
When catching `ApiErrorException` or `InvalidResponseException`, log the raw response:

```php
try {
    // API call
} catch (ApiErrorException | InvalidResponseException $e) {
    error_log(sprintf(
        "Error: %s | Raw: %s",
        $e->getMessage(),
        $e->getRawResponse()
    ));
}
```

### 4. Use Automatic Retries
Enable automatic retries to handle transient failures transparently:

```php
use VicidialApi\ValueObjects\RetryConfig;

$config = VicidialConfig::create(
    // ... other config
    retryConfig: RetryConfig::default()
);
```

### 5. Enable Logging for Production
Use PSR-3 logging to track API calls and errors:

```php
use Monolog\Logger;

$logger = new Logger('vicidial-api');
$config = VicidialConfig::create(
    // ... other config
    logger: $logger
);
```

### 6. Check Response Status
Even without exceptions, always check if the response was successful:

```php
$response = $client->agent()->dialResponse(...);

if ($response->ok()) {
    // Success
} else {
    // Handle non-success response
    error_log("Request failed: " . $response->asText());
}
```

## Migration from v1.x

### Old Approach (v1.x)
```php
try {
    $result = $api->agent()->updateFields("gabriel", $fields);
    
    if (strpos($result, 'ERROR:') !== false) {
        // Parse error manually
    }
} catch (Exception $e) {
    // Generic exception handling
}
```

### New Approach (v2.0+)
```php
use VicidialApi\Exceptions\VicidialException;
use VicidialApi\Exceptions\ApiErrorException;

try {
    $response = $client->agent()->updateFieldsResponse("gabriel", $fields);
    
    // Built-in success checking
    if ($response->ok()) {
        // Success - structured data access
        $leadId = $response->get('lead_id');
    }
} catch (ApiErrorException $e) {
    // Specific error type
    echo "API Error: " . $e->getMessage();
    echo "Raw: " . $e->getRawResponse();
} catch (VicidialException $e) {
    // Any other wrapper error
    echo "Error: " . $e->getMessage();
}
```

## See Also

- [Response DTO Guide](RESPONSE_DTO.md) - Structured response handling
- [Reliability Features](RELIABILITY_FEATURES.md) - Automatic retries, timeouts, logging
- [Migration Guide](../MIGRATION.md) - Upgrading from v1.x
