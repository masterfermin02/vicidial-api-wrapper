# Vicidial API PHP WRAPPER

Beautiful and simple Implementation to integrate Vicidial API

DISCLAIMER: VICIdial is a registered trademark of the Vicidial Group which i am not related in anyway.

VICIDIAL is a software suite that is designed to interact with the Asterisk Open-Source PBX Phone system to act as a complete inbound/outbound contact center suite with inbound email support as well.

http://www.vicidial.org/vicidial.php

Vicidial has an AGENT API and NON AGENT API, this classes are intended to make it easier to use in PHP

- http://vicidial.org/docs/NON-AGENT_API.txt
- http://vicidial.org/docs/AGENT_API.txt

## Install
Requires PHP 8.2

`composer require masterfermin02/vicidial-api-wrapper`

### For PHP 7.4+ must install this version

`composer require masterfermin02/vicidial-api-wrapper:1.0.3`

## What's New in v2.0 🎉

Version 2.0 introduces major improvements for a better developer experience:

### Configuration System
- **`VicidialConfig`**: A clean configuration object with named parameters
- **`VicidialClient`**: The new recommended client entrypoint
- **Full configuration control**: Set protocol (HTTP/HTTPS), timeout, TLS verification, custom user agent, and more
- **Better validation**: Built-in validation for configuration values
- **Backward compatible**: Legacy factory methods still work (but are deprecated)

### Response DTOs ✨ NEW
- **Structured responses**: No more regex or manual string parsing
- **Helper methods**: `ok()`, `failed()`, `asArray()`, `asText()`, `toJson()`
- **Type-safe**: Full IDE autocomplete and type checking
- **Auto-parsing**: Handles JSON, key-value, pipe-delimited, and other formats automatically
- **Request tracking**: Optional request ID for debugging
- **Opt-in**: Original methods still work, upgrade at your own pace

See the [Response DTO Guide](RESPONSE_DTO.md) for details.

```php
// New way (v2.0+)
$config = VicidialConfig::create(
    host: 'vicidial.example.com',
    user: 'api_user',
    pass: 'api_password',
    protocol: 'https',
    timeout: 60
);
$client = VicidialClient::make($config);
```

See the [Migration Guide](MIGRATION.md) for upgrade instructions.

## Authentication

This wrapper supports two authentication modes and provides both a modern configuration-based approach and legacy factory methods.

### Recommended: Using VicidialConfig (v2.0+)

The recommended way to create a client is using the `VicidialConfig` object, which provides a clean, explicit way to configure all client options:

#### Standard Authentication
```php
use VicidialApi\VicidialConfig;
use VicidialApi\VicidialClient;

$config = VicidialConfig::create(
    host: getenv('YOUR_VICIDIAL_IP'),
    user: getenv('YOUR_VICIDIAL_USER'),
    pass: getenv('YOUR_VICIDIAL_PASSWORD'),
    protocol: 'https',           // Optional: 'http' or 'https' (default: 'http')
    verifyTls: true,             // Optional: Verify TLS certificates (default: true)
    timeout: 30,                 // Optional: Request timeout in seconds (default: 30)
    urlEncodeDefault: false,     // Optional: URL encode by default (default: false)
    userAgent: 'MyApp/1.0',      // Optional: Custom user agent
    source: 'myapp'              // Optional: Source identifier (default: 'test')
);

$client = VicidialClient::make($config);
// or
$client = VicidialApi::client($config);
```

#### Basic Authentication (Remote Agent)
```php
use VicidialApi\VicidialConfig;
use VicidialApi\VicidialClient;

$config = VicidialConfig::createWithBasicAuth(
    host: getenv('YOUR_VICIDIAL_IP'),
    user: getenv('YOUR_VICIDIAL_USER'),
    pass: getenv('YOUR_VICIDIAL_PASSWORD'),
    basicAuthUser: getenv('YOUR_VICIDIAL_API_USER'),      // HTTP Basic Auth user
    basicAuthPass: getenv('YOUR_VICIDIAL_API_PASSWORD'),  // HTTP Basic Auth password
    protocol: 'https',
    timeout: 60
);

$client = VicidialClient::make($config);
```

### Response DTOs (New in v2.0)

All API methods now have Response DTO variants that return structured response objects instead of raw strings:

```php
// New Response DTO methods (add "Response" suffix to any method)
$response = $client->agent()->updateFieldsResponse("gabriel", [
    'first_name' => 'John',
    'last_name' => 'Doe'
]);

// Built-in success checking - no regex needed!
if ($response->ok()) {
    echo "Success!";
    
    // Direct data access
    $leadId = $response->get('lead_id');
    
    // Multiple output formats
    $array = $response->asArray();
    $json = $response->toJson();
    $text = $response->asText();
}

// Original methods still work for backward compatibility
$rawString = $client->agent()->updateFields("gabriel", $fields);
```

See the [Response DTO Guide](RESPONSE_DTO.md) for complete documentation.

### Legacy: Factory Methods (Deprecated)

The original factory methods are still supported for backward compatibility but are deprecated in favor of the configuration object.

#### Standard Authentication (API User/Pass)
Use `VicidialApi::create()` for standard API authentication with user credentials. This is the most common method for Agent and Admin API operations.

```php
$api = VicidialApi::create(
    getenv('YOUR_VICIDIAL_IP'),
    getenv('YOUR_VICIDIAL_USER'),
    getenv('YOUR_VICIDIAL_PASSWORD')
);
```

#### Basic Authentication (Remote Agent)
Use `VicidialApi::createWithBasicAuth()` for remote agent operations that require HTTP Basic Authentication along with agent credentials.

```php
$api = VicidialApi::createWithBasicAuth(
    getenv('YOUR_VICIDIAL_IP'),
    getenv('YOUR_VICIDIAL_API_USER'),      // HTTP Basic Auth user
    getenv('YOUR_VICIDIAL_API_PASSWORD'),   // HTTP Basic Auth password
    getenv('YOUR_VICIDIAL_REMOTE_AGENT'),   // Agent username
    getenv('YOUR_VICIDIAL_REMOTE_PASSWORD') // Agent password
);
```

## How to use it

> 💡 **Tip**: See [example/using_config.php](example/using_config.php) for a comprehensive demonstration of all configuration options.
> 
> 📖 **Migrating from v1.x?** Check out the [Migration Guide](MIGRATION.md) for step-by-step instructions.

### Recommended Examples (Using VicidialConfig)

#### Example: Complete workflow with configuration object
```php
<?php

require_once 'vendor/autoload.php';

use VicidialApi\VicidialConfig;
use VicidialApi\VicidialClient;

// Create configuration
$config = VicidialConfig::create(
    host: getenv('YOUR_VICIDIAL_IP'),
    user: getenv('YOUR_VICIDIAL_USER'),
    pass: getenv('YOUR_VICIDIAL_PASSWORD'),
    protocol: 'https',
    timeout: 30
);

// Create client
$client = VicidialClient::make($config);

try {
    // Update fields on agent screen
    $fields = [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'address1' => '123 Fake St'
    ];
    echo $client->agent()->updateFields("gabriel", $fields);
    
    // Make an external dial
    echo $client->agent()->dial("gabriel", [
        'phone_number' => '1234567890',
        'phone_code' => '1'
    ]);
    
    // Use admin API
    echo $client->admin()->mohList([
        'format' => 'selectframe'
    ]);
    
} catch (Exception $e) {
    echo 'Exception: ', $e->getMessage(), "\n";
}
```

#### Example: Remote agent with Basic Authentication
```php
<?php

require_once 'vendor/autoload.php';

use VicidialApi\VicidialConfig;
use VicidialApi\VicidialClient;

$config = VicidialConfig::createWithBasicAuth(
    host: getenv('YOUR_VICIDIAL_IP'),
    user: getenv('YOUR_VICIDIAL_USER'),
    pass: getenv('YOUR_VICIDIAL_PASSWORD'),
    basicAuthUser: getenv('YOUR_VICIDIAL_API_USER'),
    basicAuthPass: getenv('YOUR_VICIDIAL_API_PASSWORD')
);

$client = VicidialClient::make($config);

try {
    $remoteAgent = $client->remoteAgent();
    
    $remoteAgent->active(
        getenv('agentId'),
        getenv('confExten')
    );
    
    $remoteAgent->hangUp("gabriel", [
        'status' => 'SALE'
    ]);
    
    $remoteAgent->inActive(
        getenv('agentId'),
        getenv('confExten')
    );
} catch (Exception $e) {
    echo 'Exception: ', $e->getMessage(), "\n";
}
```

---

### Legacy Examples (Deprecated)

> **Note**: The following examples use the legacy factory methods. While still supported, we recommend using `VicidialConfig` and `VicidialClient` as shown above.

### Example 1: Update fields on agent screen
```php

<?php

$fields['first_name'] = "John";
$fields['last_name'] = "Doe";
$fields['address1'] = "123 Fake St";

try {
     $agentApi = VicidialApi::create(
        getenv('YOUR_VICIDIAL_IP'),
        getenv('YOUR_VICIDIAL_USER'),
        getenv('YOUR_VICIDIAL_PASSWORD')
     )->agent();
     echo $agentApi->updateFields("gabriel", $fields);
} catch (Exception $e) {
     echo 'Exception: ',  $e->getMessage(), "\n";
}

```

### Example 2: Hangup Call, Dispo it and Pause Agent
```php

<?php

try {
     $agentApi = VicidialApi::create(
        getenv('YOUR_VICIDIAL_IP'),
        getenv('YOUR_VICIDIAL_USER'),
        getenv('YOUR_VICIDIAL_PASSWORD')
     )->agent();
     $agentApi->pause("gabriel", "PAUSE");
     $agentApi->hangup("gabriel");
     $agentApi->dispo("gabriel", ['value' => 'SALE']);
     $agentApi->pauseCode("gabriel", "BREAK");
} catch (Exception $e) {
     echo 'Exception: ',  $e->getMessage(), "\n";
}

```

### Example 3: Make an external dial call
```php
<?php

try {
     $agentApi = VicidialApi::create(
         getenv('YOUR_VICIDIAL_IP'),
         getenv('YOUR_VICIDIAL_USER'),
         getenv('YOUR_VICIDIAL_PASSWORD')
     )->agent();
     echo $agentApi->dial("gabriel", [
         'phone_number' => '1234567890',
         'phone_code' => '1',
         'search' => 'YES',
         'preview' => 'NO',
         'focus' => 'YES'
     ]);
} catch (Exception $e) {
     echo 'Exception: ',  $e->getMessage(), "\n";
}

```

### Example 4: Using the Admin API (Non-Agent API)
```php
    <?php
    
    require_once 'vendor/autoload.php';
    
    try {
        $agentApi = VicidialApi::create(
            getenv('YOUR_VICIDIAL_IP'),
            getenv('YOUR_VICIDIAL_USER'),
            getenv('YOUR_VICIDIAL_PASSWORD')
        )->admin();
        echo $agentApi->mohList([
            'format' => 'selectframe',
            'comments' => 'fieldname',
            'stage' => 'date'
        ]);
    } catch (Exception $e) {
        echo 'Exception: ',  $e->getMessage(), "\n";
    }
```

### Example 5: Using URL encode for special characters
```php
    <?php
    
    require_once 'vendor/autoload.php';

    $fields['first_name'] = "John";
    $fields['last_name']  = "Doe";
    $fields['address1']   = "123 Fake St";
    $fields['phone_number']   = "18002474747";
    
    try {
        $agentApi = VicidialApi::create(
            getenv('YOUR_VICIDIAL_IP'),
            getenv('YOUR_VICIDIAL_USER'),
            getenv('YOUR_VICIDIAL_PASSWORD'),
            "test",
            false
        )->admin();
        echo $agentApi
        ->withUrlEncode(true)
        ->addLead($fields);
    } catch (Exception $e) {
        echo 'Exception: ', $e->getMessage(), "\n";
    }
```

### Example 6: Remote agent login with Basic Authentication

```php
    <?php
    
    require_once 'vendor/autoload.php';
    
    try {
        $remoteAgent = VicidialApi::createWithBasicAuth(
            getenv('YOUR_VICIDIAL_IP'),
            getenv('YOUR_VICIDIAL_API_USER'),
            getenv('YOUR_VICIDIAL_API_PASSWORD'),
            getenv('YOUR_VICIDIAL_REMOTE_AGENT'),
            getenv('YOUR_VICIDIAL_REMOTE_PASSWORD'),
        )->remoteAgent();
        $remoteAgent->active(
            getenv('agentId'),
            getenv('confExten'),
        );
        $remoteAgent->hangUp(
            "gabriel",
            [
                'status' => 'SALE',
            ]
        );
        $remoteAgent->inActive(
            getenv('agentId'),
            getenv('confExten'),
        );
    } catch (Exception $e) {
        echo 'Exception: ',  $e->getMessage(), "\n";
    }
```


### Docs:
- [Agent](https://github.com/masterfermin02/vicidial-api-wrapper/blob/main/docs/agent.md)
- [Admin](https://github.com/masterfermin02/vicidial-api-wrapper/blob/main/docs/admin.md)
