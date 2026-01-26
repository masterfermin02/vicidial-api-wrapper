<?php

require_once __DIR__.'/../vendor/autoload.php';

use VicidialApi\VicidialClient;
use VicidialApi\VicidialConfig;

/**
 * Example: Using VicidialResponse DTOs for structured response handling
 *
 * This demonstrates the new Response DTO feature that provides:
 * - Structured access to response data
 * - Helper methods: ok(), asArray(), asText()
 * - Parsed response data without regex/string parsing
 * - Request ID tracking
 *
 * Vicidial API returns responses in various formats:
 * - SUCCESS: message - data|more_data (most common)
 * - ERROR: message - data|more_data
 * - VERSION: 2.0.5|BUILD: 12345|DATE: 2009-01-15 (version info)
 * - field1|field2|field3 (pipe-delimited lists)
 * - key: value (multi-line key-value pairs)
 */

// Create configuration
$config = VicidialConfig::create(
    host: getenv('YOUR_VICIDIAL_IP') ?: 'localhost',
    user: getenv('YOUR_VICIDIAL_USER') ?: 'testuser',
    pass: getenv('YOUR_VICIDIAL_PASSWORD') ?: 'testpass',
    protocol: 'https',
    timeout: 30
);

// Create client
$client = VicidialClient::make($config);

echo "=== Vicidial Response DTO Examples ===\n\n";

// Example 1: Update fields with Response DTO
echo "1. Update Fields with Response DTO:\n";
try {
    $response = $client->agent()->updateFieldsResponse('gabriel', [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'address1' => '123 Fake St',
    ]);

    echo '   Status: '.$response->status()."\n";
    echo '   Success: '.($response->ok() ? 'Yes' : 'No')."\n";
    echo '   Raw: '.$response->raw()."\n";

    if ($response->has('lead_id')) {
        echo '   Lead ID: '.$response->get('lead_id')."\n";
    }

    if ($response->requestId()) {
        echo '   Request ID: '.$response->requestId()."\n";
    }

    echo "\n";
} catch (Exception $e) {
    echo '   Error: '.$e->getMessage()."\n\n";
}

// Example 2: Dial with Response DTO
echo "2. External Dial with Response DTO:\n";
try {
    $response = $client->agent()->dialResponse('gabriel', [
        'phone_number' => '1234567890',
        'phone_code' => '1',
    ]);

    if ($response->ok()) {
        echo "   ✓ Call initiated successfully\n";
        echo "   Response data:\n";
        foreach ($response->asArray() as $key => $value) {
            echo "     - $key: $value\n";
        }
    } else {
        echo "   ✗ Call failed\n";
        echo '   Error: '.$response->raw()."\n";
    }

    echo "\n";
} catch (Exception $e) {
    echo '   Error: '.$e->getMessage()."\n\n";
}

// Example 3: Admin API with Response DTO
echo "3. Get MOH List with Response DTO:\n";
try {
    $response = $client->admin()->mohListResponse([
        'format' => 'selectframe',
    ]);

    echo '   Status: '.$response->status()."\n";
    echo '   Raw response: '.substr($response->asText(), 0, 100)."...\n";

    // Access parsed data
    $parsed = $response->parsed();
    echo '   Parsed fields: '.count($parsed)."\n";

    echo "\n";
} catch (Exception $e) {
    echo '   Error: '.$e->getMessage()."\n\n";
}

// Example 4: Pause agent with Response DTO
echo "4. Pause Agent with Response DTO:\n";
try {
    $response = $client->agent()->pauseResponse('gabriel', 'PAUSE');

    // Use helper method to check success
    if ($response->ok()) {
        echo "   ✓ Agent paused successfully\n";
    } else {
        echo "   ✗ Failed to pause agent\n";
    }

    // Convert to JSON for logging or API responses
    echo '   JSON: '.$response->toJson()."\n";

    echo "\n";
} catch (Exception $e) {
    echo '   Error: '.$e->getMessage()."\n\n";
}

// Example 5: Version check with Response DTO
echo "5. Version Check with Response DTO:\n";
try {
    $response = $client->admin()->versionResponse();

    echo '   Vicidial Version: '.$response->asText()."\n";

    // Use array access
    if (isset($response['version'])) {
        echo '   Version field: '.$response['version']."\n";
    }

    echo "\n";
} catch (Exception $e) {
    echo '   Error: '.$e->getMessage()."\n\n";
}

// Example 6: Building reliable logic with Response DTOs
echo "6. Building Reliable Logic:\n";
try {
    $hangupResponse = $client->agent()->hangupResponse('gabriel');

    if ($hangupResponse->ok()) {
        echo "   ✓ Hangup successful, proceeding with disposition\n";

        $dispoResponse = $client->agent()->dispoResponse('gabriel', ['value' => 'SALE']);

        if ($dispoResponse->ok()) {
            echo "   ✓ Disposition set successfully\n";

            // Log for analytics
            $logData = [
                'action' => 'hangup_and_dispo',
                'agent' => 'gabriel',
                'hangup_status' => $hangupResponse->status(),
                'dispo_status' => $dispoResponse->status(),
                'timestamp' => date('Y-m-d H:i:s'),
            ];

            echo '   Log: '.json_encode($logData)."\n";
        } else {
            echo '   ✗ Disposition failed: '.$dispoResponse->raw()."\n";
        }
    } else {
        echo "   ✗ Hangup failed, skipping disposition\n";
    }

    echo "\n";
} catch (Exception $e) {
    echo '   Error: '.$e->getMessage()."\n\n";
}

echo "=== Examples Complete ===\n";
