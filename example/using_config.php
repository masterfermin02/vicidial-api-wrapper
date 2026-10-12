<?php

/**
 * Example demonstrating the new VicidialConfig approach
 *
 * This is the recommended way to use the Vicidial API wrapper (v2.0+)
 */

require_once __DIR__.'/../vendor/autoload.php';

use VicidialApi\VicidialClient;
use VicidialApi\VicidialConfig;

// Example 1: Basic configuration with defaults
echo "=== Example 1: Basic Configuration ===\n";

$config = VicidialConfig::create(
    host: 'your-vicidial-server.com',
    user: 'api_user',
    pass: 'api_password'
);

$client = VicidialClient::make($config);

// Example 2: Advanced configuration with all options
echo "\n=== Example 2: Advanced Configuration ===\n";

$config = VicidialConfig::create(
    host: 'your-vicidial-server.com',
    user: 'api_user',
    pass: 'api_password',
    protocol: 'https',              // Use HTTPS for secure connections
    verifyTls: true,                // Verify SSL/TLS certificates
    timeout: 60,                    // 60 second timeout for requests
    urlEncodeDefault: false,        // Deprecated no-op: values are always URL-encoded once
    userAgent: 'MyApp/1.0',        // Custom user agent
    source: 'myapp'                 // Source identifier for tracking
);

$client = VicidialClient::make($config);

// Or use the VicidialApi facade
// $client = VicidialApi::client($config);

// Example 3: Using the client to interact with Agent API
echo "\n=== Example 3: Agent API Operations ===\n";

try {
    // Update agent screen fields
    $fields = [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'address1' => '123 Main St',
        'city' => 'Springfield',
        'state' => 'IL',
        'postal_code' => '62701',
    ];

    $response = $client->agent()->updateFields('agent_username', $fields);
    echo "Update Fields Response: $response\n";

    // Make an outbound call
    $dialResponse = $client->agent()->dial('agent_username', [
        'phone_number' => '1234567890',
        'phone_code' => '1',
        'search' => 'YES',
        'preview' => 'NO',
        'focus' => 'YES',
    ]);
    echo "Dial Response: $dialResponse\n";

    // Pause the agent
    $pauseResponse = $client->agent()->pause('agent_username', 'PAUSE');
    echo "Pause Response: $pauseResponse\n";

    // Disposition a call
    $dispoResponse = $client->agent()->dispo('agent_username', [
        'value' => 'SALE',
    ]);
    echo "Dispo Response: $dispoResponse\n";

} catch (Exception $e) {
    echo 'Error: '.$e->getMessage()."\n";
}

// Example 4: Using the Admin API
echo "\n=== Example 4: Admin API Operations ===\n";

try {
    // Get music on hold list
    $mohList = $client->admin()->mohList([
        'format' => 'selectframe',
        'comments' => 'fieldname',
    ]);
    echo "MOH List: $mohList\n";

    // Get version information
    $version = $client->admin()->version();
    echo "Vicidial Version: $version\n";

} catch (Exception $e) {
    echo 'Error: '.$e->getMessage()."\n";
}

// Example 5: Basic Authentication for Remote Agent
echo "\n=== Example 5: Remote Agent with Basic Auth ===\n";

$remoteConfig = VicidialConfig::createWithBasicAuth(
    host: 'your-vicidial-server.com',
    user: 'agent_user',
    pass: 'agent_password',
    basicAuthUser: 'http_auth_user',      // HTTP Basic Auth username
    basicAuthPass: 'http_auth_password',  // HTTP Basic Auth password
    protocol: 'https'
);

$remoteClient = VicidialClient::make($remoteConfig);

try {
    // Activate remote agent
    $remoteClient->remoteAgent()->active('agent_id', 'conference_extension');

    // Perform actions...
    $remoteClient->remoteAgent()->hangUp('agent_id', [
        'status' => 'SALE',
    ]);

    // Deactivate remote agent
    $remoteClient->remoteAgent()->inActive('agent_id', 'conference_extension');

    echo "Remote agent operations completed successfully\n";

} catch (Exception $e) {
    echo 'Error: '.$e->getMessage()."\n";
}

// Example 6: Using environment variables
echo "\n=== Example 6: Configuration from Environment Variables ===\n";

$envConfig = VicidialConfig::create(
    host: getenv('VICIDIAL_HOST') ?: 'localhost',
    user: getenv('VICIDIAL_USER') ?: 'user',
    pass: getenv('VICIDIAL_PASS') ?: 'pass',
    protocol: getenv('VICIDIAL_PROTOCOL') ?: 'http',
    timeout: (int) (getenv('VICIDIAL_TIMEOUT') ?: 30)
);

$envClient = VicidialClient::make($envConfig);

echo "Client configured from environment variables\n";

echo "\n=== All Examples Complete ===\n";
