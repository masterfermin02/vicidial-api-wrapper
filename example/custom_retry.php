<?php

/**
 * Example: Custom retry configuration
 *
 * This example demonstrates how to customize retry behavior for different scenarios:
 * - Aggressive retries for critical operations
 * - Conservative retries for bulk operations
 * - Selective retry conditions
 */

require_once __DIR__.'/../vendor/autoload.php';

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use VicidialApi\ValueObjects\RetryConfig;
use VicidialApi\VicidialClient;
use VicidialApi\VicidialConfig;

$logger = new Logger('vicidial-api');
$logger->pushHandler(new StreamHandler('php://stdout', Logger::INFO));

echo "🔄 Custom Retry Configuration Examples\n";
echo str_repeat('=', 70)."\n\n";

// ============================================================================
// Example 1: Aggressive retry for critical operations
// ============================================================================
echo "📌 Example 1: Aggressive retry (critical operations)\n";
echo str_repeat('-', 70)."\n";

$aggressiveRetry = RetryConfig::custom(
    maxAttempts: 5,              // Try up to 5 times
    initialDelayMs: 500,         // Start quickly (500ms)
    maxDelayMs: 5000,            // Cap at 5 seconds
    multiplier: 1.5,             // Moderate growth
    retryOn5xx: true,
    retryOnNetworkErrors: true,
    retryOn429: true,
);

$criticalConfig = VicidialConfig::create(
    host: getenv('VICIDIAL_HOST') ?: 'vicidial.example.com',
    user: getenv('VICIDIAL_USER') ?: 'api_user',
    pass: getenv('VICIDIAL_PASS') ?: 'api_password',
    timeout: 30,
    connectTimeout: 5,
    retryConfig: $aggressiveRetry,
    logger: $logger,
);

echo "Configuration:\n";
echo "  - Max attempts: 5\n";
echo "  - Initial delay: 500ms\n";
echo "  - Multiplier: 1.5x\n";
echo "  - Max delay: 5s\n";
echo "\nRetry schedule:\n";
for ($i = 1; $i <= 5; $i++) {
    $delay = $aggressiveRetry->calculateDelay($i);
    echo "  Attempt $i: ".($i === 1 ? 'immediate' : "~{$delay}ms delay")."\n";
}
echo "\n";

// ============================================================================
// Example 2: Conservative retry for bulk operations
// ============================================================================
echo "📌 Example 2: Conservative retry (bulk operations)\n";
echo str_repeat('-', 70)."\n";

$conservativeRetry = RetryConfig::custom(
    maxAttempts: 3,              // Only 3 attempts
    initialDelayMs: 2000,        // Start with 2 seconds
    maxDelayMs: 30000,           // Up to 30 seconds
    multiplier: 3.0,             // Aggressive backoff
    retryOn5xx: true,
    retryOnNetworkErrors: true,
    retryOn429: true,
);

$bulkConfig = VicidialConfig::create(
    host: getenv('VICIDIAL_HOST') ?: 'vicidial.example.com',
    user: getenv('VICIDIAL_USER') ?: 'api_user',
    pass: getenv('VICIDIAL_PASS') ?: 'api_password',
    timeout: 180,                // Long timeout for bulk ops
    connectTimeout: 10,
    retryConfig: $conservativeRetry,
    logger: $logger,
);

echo "Configuration:\n";
echo "  - Max attempts: 3\n";
echo "  - Initial delay: 2000ms\n";
echo "  - Multiplier: 3.0x\n";
echo "  - Max delay: 30s\n";
echo "\nRetry schedule:\n";
for ($i = 1; $i <= 3; $i++) {
    $delay = $conservativeRetry->calculateDelay($i);
    echo "  Attempt $i: ".($i === 1 ? 'immediate' : "~{$delay}ms delay")."\n";
}
echo "\n";

// ============================================================================
// Example 3: Network errors only (skip HTTP errors)
// ============================================================================
echo "📌 Example 3: Network errors only (no HTTP retry)\n";
echo str_repeat('-', 70)."\n";

$networkOnlyRetry = RetryConfig::custom(
    maxAttempts: 3,
    initialDelayMs: 1000,
    maxDelayMs: 10000,
    multiplier: 2.0,
    retryOn5xx: false,           // Don't retry 5xx
    retryOnNetworkErrors: true,  // Only network errors
    retryOn429: false,           // Don't retry rate limits
);

$networkConfig = VicidialConfig::create(
    host: getenv('VICIDIAL_HOST') ?: 'vicidial.example.com',
    user: getenv('VICIDIAL_USER') ?: 'api_user',
    pass: getenv('VICIDIAL_PASS') ?: 'api_password',
    retryConfig: $networkOnlyRetry,
    logger: $logger,
);

echo "Configuration:\n";
echo "  - Retry on 5xx: NO\n";
echo "  - Retry on network errors: YES\n";
echo "  - Retry on 429: NO\n";
echo "\n";

// ============================================================================
// Example 4: No retries (fast-fail)
// ============================================================================
echo "📌 Example 4: No retries (fast-fail mode)\n";
echo str_repeat('-', 70)."\n";

$noRetryConfig = VicidialConfig::create(
    host: getenv('VICIDIAL_HOST') ?: 'vicidial.example.com',
    user: getenv('VICIDIAL_USER') ?: 'api_user',
    pass: getenv('VICIDIAL_PASS') ?: 'api_password',
    timeout: 15,
    connectTimeout: 3,
    retryConfig: RetryConfig::disabled(), // No retries
    logger: $logger,
);

echo "Configuration:\n";
echo "  - Retries: DISABLED\n";
echo "  - Fail immediately on any error\n";
echo "\n";

// ============================================================================
// Practical Usage Examples
// ============================================================================
echo str_repeat('=', 70)."\n";
echo "🎯 Practical Usage\n";
echo str_repeat('=', 70)."\n\n";

try {
    // Use aggressive retry for critical operations
    echo "1️⃣  Critical: Update agent status (aggressive retry)\n";
    $criticalClient = VicidialClient::make($criticalConfig);
    $result = $criticalClient->agent()->pause('agent001', 'PAUSE');
    echo "   ✓ Success\n\n";

} catch (Exception $e) {
    echo '   ✗ Failed after 5 attempts: '.$e->getMessage()."\n\n";
}

try {
    // Use conservative retry for bulk operations
    echo "2️⃣  Bulk: Export agent stats (conservative retry)\n";
    $bulkClient = VicidialClient::make($bulkConfig);
    $result = $bulkClient->admin()->agentStatsExport([
        'query_date' => date('Y-m-d'),
    ]);
    echo "   ✓ Success\n\n";

} catch (Exception $e) {
    echo '   ✗ Failed after 3 attempts: '.$e->getMessage()."\n\n";
}

try {
    // Use network-only retry for external triggers
    echo "3️⃣  External: Trigger via network (network errors only)\n";
    $networkClient = VicidialClient::make($networkConfig);
    $result = $networkClient->agent()->webserver();
    echo "   ✓ Success\n\n";

} catch (Exception $e) {
    echo '   ✗ Failed: '.$e->getMessage()."\n\n";
}

echo str_repeat('=', 70)."\n";
echo "💡 Recommendations:\n";
echo "   - Critical ops: Use aggressive retry (5 attempts, short delays)\n";
echo "   - Bulk ops: Use conservative retry (3 attempts, long delays)\n";
echo "   - Real-time: Use fast-fail (no retries) for immediate feedback\n";
echo "   - Network issues: Retry network errors, skip HTTP errors\n";
