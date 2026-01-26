<?php

/**
 * Example: Error Handling with Vicidial API Wrapper
 *
 * This example demonstrates how to use the structured exception hierarchy
 * to handle different types of errors when working with the Vicidial API.
 */

require_once __DIR__.'/../vendor/autoload.php';

use VicidialApi\Exceptions\ApiErrorException;
use VicidialApi\Exceptions\AuthenticationException;
use VicidialApi\Exceptions\ConfigurationException;
use VicidialApi\Exceptions\InvalidResponseException;
use VicidialApi\Exceptions\RetryException;
use VicidialApi\Exceptions\TransportException;
use VicidialApi\Exceptions\VicidialException;
use VicidialApi\ValueObjects\RetryConfig;
use VicidialApi\VicidialClient;
use VicidialApi\VicidialConfig;

// Example 1: Simple error handling - catch all errors
function example1_catchAllErrors(): void
{
    echo "=== Example 1: Catch All Errors ===\n";

    try {
        $config = VicidialConfig::create(
            host: getenv('YOUR_VICIDIAL_IP') ?: 'demo.vicidial.com',
            user: getenv('YOUR_VICIDIAL_USER') ?: 'test_user',
            pass: getenv('YOUR_VICIDIAL_PASSWORD') ?: 'test_pass',
            protocol: 'https'
        );

        $client = VicidialClient::make($config);

        $response = $client->agent()->updateFieldsResponse('gabriel', [
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        if ($response->ok()) {
            echo '✓ Success! Lead ID: '.$response->get('lead_id')."\n";
        }

    } catch (VicidialException $e) {
        // Catches all wrapper errors
        echo '✗ Error: '.$e->getMessage()."\n";
    }

    echo "\n";
}

// Example 2: Specific error handling
function example2_specificErrorHandling(): void
{
    echo "=== Example 2: Specific Error Handling ===\n";

    try {
        $config = VicidialConfig::create(
            host: getenv('YOUR_VICIDIAL_IP') ?: 'demo.vicidial.com',
            user: getenv('YOUR_VICIDIAL_USER') ?: 'test_user',
            pass: getenv('YOUR_VICIDIAL_PASSWORD') ?: 'test_pass'
        );

        $client = VicidialClient::make($config);

        $response = $client->agent()->dialResponse('gabriel', [
            'phone_number' => '1234567890',
            'phone_code' => '1',
        ]);

        if ($response->ok()) {
            echo "✓ Call initiated successfully\n";
        }

    } catch (AuthenticationException $e) {
        echo '✗ Authentication failed: '.$e->getMessage()."\n";
        echo "  Action: Check your API credentials\n";

    } catch (TransportException $e) {
        echo '✗ Network error: '.$e->getMessage()."\n";
        echo "  Action: Check network connectivity and retry\n";

    } catch (ApiErrorException $e) {
        echo '✗ API error: '.$e->getMessage()."\n";
        echo '  Raw response: '.$e->getRawResponse()."\n";
        echo "  Action: Check API parameters\n";

    } catch (InvalidResponseException $e) {
        echo '✗ Invalid response: '.$e->getMessage()."\n";
        echo '  Raw response: '.$e->getRawResponse()."\n";
        echo "  Action: Check API version compatibility\n";

    } catch (RetryException $e) {
        echo '✗ Failed after '.$e->getAttemptsMade()." attempts\n";
        echo '  Error: '.$e->getMessage()."\n";
        echo "  Action: Service may be down, escalate or queue for later\n";

    } catch (VicidialException $e) {
        echo '✗ Unexpected error: '.$e->getMessage()."\n";
    }

    echo "\n";
}

// Example 3: Configuration error handling
function example3_configurationErrors(): void
{
    echo "=== Example 3: Configuration Error Handling ===\n";

    try {
        // This will throw ConfigurationException
        $config = VicidialConfig::create(
            host: '',  // Invalid empty host
            user: 'test_user',
            pass: 'test_pass'
        );

    } catch (ConfigurationException $e) {
        echo '✗ Configuration error: '.$e->getMessage()."\n";
        echo "  Action: Fix configuration before proceeding\n";
    }

    try {
        // This will throw ConfigurationException
        $config = VicidialConfig::create(
            host: 'demo.vicidial.com',
            user: 'test_user',
            pass: 'test_pass',
            timeout: -5  // Invalid negative timeout
        );

    } catch (ConfigurationException $e) {
        echo '✗ Configuration error: '.$e->getMessage()."\n";
        echo "  Action: Use positive timeout value\n";
    }

    echo "\n";
}

// Example 4: Retry handling
function example4_retryHandling(): void
{
    echo "=== Example 4: Retry Handling ===\n";

    try {
        $config = VicidialConfig::create(
            host: getenv('YOUR_VICIDIAL_IP') ?: 'demo.vicidial.com',
            user: getenv('YOUR_VICIDIAL_USER') ?: 'test_user',
            pass: getenv('YOUR_VICIDIAL_PASSWORD') ?: 'test_pass',
            timeout: 30,
            retryConfig: RetryConfig::create(
                maxAttempts: 3,
                initialDelayMs: 500,
                maxDelayMs: 5000,
                multiplier: 2.0
            )
        );

        $client = VicidialClient::make($config);

        echo "Attempting API call with automatic retries...\n";

        $response = $client->agent()->updateFieldsResponse('gabriel', [
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        if ($response->ok()) {
            echo '✓ Success after retries! Lead ID: '.$response->get('lead_id')."\n";
        }

    } catch (RetryException $e) {
        echo '✗ All '.$e->getAttemptsMade()." retry attempts failed\n";
        echo '  Error: '.$e->getMessage()."\n";
        echo "  Action: Queue for later processing or escalate\n";

    } catch (VicidialException $e) {
        echo '✗ Error: '.$e->getMessage()."\n";
    }

    echo "\n";
}

// Example 5: Production-ready error handling
function example5_productionErrorHandling(): void
{
    echo "=== Example 5: Production Error Handling ===\n";

    $leadData = [
        'agent' => 'gabriel',
        'fields' => [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'phone_number' => '1234567890',
        ],
    ];

    try {
        $config = VicidialConfig::create(
            host: getenv('YOUR_VICIDIAL_IP') ?: 'demo.vicidial.com',
            user: getenv('YOUR_VICIDIAL_USER') ?: 'test_user',
            pass: getenv('YOUR_VICIDIAL_PASSWORD') ?: 'test_pass',
            protocol: 'https',
            timeout: 60,
            retryConfig: RetryConfig::default()
        );

        $client = VicidialClient::make($config);

        $response = $client->agent()->updateFieldsResponse(
            $leadData['agent'],
            $leadData['fields']
        );

        if ($response->ok()) {
            echo "✓ Lead updated successfully\n";
            echo '  Lead ID: '.$response->get('lead_id')."\n";

            // Mark as processed in your system
            markLeadAsProcessed($leadData);
        } else {
            echo "⚠ Lead update returned non-success response\n";
            echo '  Response: '.$response->asText()."\n";
        }

    } catch (AuthenticationException $e) {
        echo "✗ CRITICAL: Authentication failed\n";
        error_log('[CRITICAL] Vicidial authentication failed: '.$e->getMessage());
        sendAdminAlert('Vicidial API credentials invalid - check configuration');

    } catch (TransportException|RetryException $e) {
        echo "✗ Transient error, queueing for retry\n";
        error_log('[ERROR] Transient error: '.$e->getMessage());
        queueLeadForRetry($leadData);

    } catch (ApiErrorException $e) {
        echo '✗ API error: '.$e->getMessage()."\n";
        error_log('[ERROR] API error: '.$e->getMessage().' | Raw: '.$e->getRawResponse());

        // Handle specific API errors
        if (str_contains($e->getMessage(), 'no agent_user')) {
            echo "  Action: Agent '".$leadData['agent']."' not found\n";
            handleMissingAgent($leadData['agent']);
        }

    } catch (InvalidResponseException $e) {
        echo "✗ Invalid response format\n";
        error_log('[ERROR] Invalid response: '.$e->getMessage().' | Raw: '.$e->getRawResponse());
        sendAdminAlert('Unexpected response format - possible API version mismatch');

    } catch (VicidialException $e) {
        echo '✗ Unexpected error: '.$e->getMessage()."\n";
        error_log('[ERROR] Unexpected error: '.get_class($e).' - '.$e->getMessage());
    }

    echo "\n";
}

// Helper functions (stub implementations)
function markLeadAsProcessed(array $leadData): void
{
    echo "  [DB] Lead marked as processed\n";
}

function queueLeadForRetry(array $leadData): void
{
    echo "  [QUEUE] Lead queued for retry with backoff\n";
}

function handleMissingAgent(string $agent): void
{
    echo "  [ACTION] Creating support ticket for missing agent: $agent\n";
}

function sendAdminAlert(string $message): void
{
    echo "  [ALERT] Admin notification sent: $message\n";
}

// Run all examples
echo "\n";
echo "╔═══════════════════════════════════════════════════════════════╗\n";
echo "║         Vicidial API Wrapper - Error Handling Examples        ║\n";
echo "╚═══════════════════════════════════════════════════════════════╝\n";
echo "\n";

example1_catchAllErrors();
example2_specificErrorHandling();
example3_configurationErrors();
example4_retryHandling();
example5_productionErrorHandling();

echo "╔═══════════════════════════════════════════════════════════════╗\n";
echo "║  See docs/ERROR_HANDLING.md for complete documentation        ║\n";
echo "╚═══════════════════════════════════════════════════════════════╝\n";
