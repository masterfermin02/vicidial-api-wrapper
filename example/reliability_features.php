<?php

/**
 * Example: Using reliability features (timeouts, retries, logging)
 *
 * This example demonstrates how to configure the Vicidial API wrapper
 * for production use with proper timeout configuration, automatic retries,
 * and comprehensive logging.
 */

require_once __DIR__.'/../vendor/autoload.php';

use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use VicidialApi\Exceptions\TransporterException;
use VicidialApi\ValueObjects\RetryConfig;
use VicidialApi\VicidialClient;
use VicidialApi\VicidialConfig;

// Setup logger (Monolog)
$logger = new Logger('vicidial-api');

// File handler for all logs (JSON format)
$fileHandler = new StreamHandler(__DIR__.'/../logs/vicidial.log', Logger::DEBUG);
$fileHandler->setFormatter(new JsonFormatter);
$logger->pushHandler($fileHandler);

// Error file handler (rotate daily, keep 7 days)
$errorHandler = new RotatingFileHandler(
    __DIR__.'/../logs/vicidial-errors.log',
    7, // Keep 7 days
    Logger::ERROR
);
$logger->pushHandler($errorHandler);

// Create configuration with reliability features
$config = VicidialConfig::create(
    host: getenv('VICIDIAL_HOST') ?: 'vicidial.example.com',
    user: getenv('VICIDIAL_USER') ?: 'api_user',
    pass: getenv('VICIDIAL_PASS') ?: 'api_password',
    protocol: 'https',
    timeout: 60,           // Total request timeout: 60 seconds
    connectTimeout: 10,    // Connection timeout: 10 seconds
    retryConfig: RetryConfig::default(), // Enable retries with defaults
    logger: $logger,       // Enable logging
);

echo "🚀 Vicidial API Client with Reliability Features\n";
echo str_repeat('=', 60)."\n\n";

try {
    $client = VicidialClient::make($config);

    echo "✓ Client created successfully\n";
    echo "  - Connect timeout: 10s\n";
    echo "  - Request timeout: 60s\n";
    echo "  - Retries enabled: 3 attempts\n";
    echo '  - Logging enabled: '.(__DIR__.'/../logs/vicidial.log')."\n\n";

    // Example 1: Simple API call with automatic retries and logging
    echo "📞 Example 1: Get agent status\n";
    echo str_repeat('-', 60)."\n";

    $response = $client->admin()->agentStatus([
        'agent_user' => 'agent001',
    ]);

    echo "Response received:\n";
    echo substr($response, 0, 200)."...\n\n";

    // Example 2: Operation that might need retries
    echo "📊 Example 2: Update lead (with retry protection)\n";
    echo str_repeat('-', 60)."\n";

    $updateResponse = $client->admin()->updateLead([
        'lead_id' => '12345',
        'phone_number' => '5551234567',
        'first_name' => 'John',
        'last_name' => 'Doe',
    ]);

    echo "Lead updated successfully\n";
    echo substr($updateResponse, 0, 200)."...\n\n";

    // Example 3: Using Response DTO with retries
    echo "📋 Example 3: Get campaign list (Response DTO)\n";
    echo str_repeat('-', 60)."\n";

    $campaignsResponse = $client->admin()->campaignsListResponse([
        'stage' => 'ALL',
    ]);

    if ($campaignsResponse->ok()) {
        echo "✓ Campaigns retrieved successfully\n";
        $campaigns = $campaignsResponse->asArray();
        echo '  Found '.count($campaigns)." campaigns\n\n";
    } else {
        echo "✗ Failed to retrieve campaigns\n\n";
    }

    echo "✓ All operations completed successfully!\n";
    echo "\n📝 Check logs for detailed request information:\n";
    echo '   '.__DIR__."/../logs/vicidial.log\n";

} catch (TransporterException $e) {
    echo "❌ Network/Transport Error (after retries):\n";
    echo '   '.$e->getMessage()."\n";
    echo "\n📝 Check error logs:\n";
    echo '   '.__DIR__."/../logs/vicidial-errors.log\n";
} catch (Exception $e) {
    echo '❌ Error: '.$e->getMessage()."\n";
    echo "\n📝 Check error logs:\n";
    echo '   '.__DIR__."/../logs/vicidial-errors.log\n";
}

echo "\n".str_repeat('=', 60)."\n";
echo "💡 Tips:\n";
echo "   - All requests are automatically logged with timing info\n";
echo "   - Failed requests are retried up to 3 times with backoff\n";
echo "   - Sensitive parameters (passwords) are redacted in logs\n";
echo "   - Network errors and 5xx responses trigger automatic retries\n";
