<?php

/*
 * Published with: php artisan vendor:publish --tag=vicidial-config
 * Every key maps onto a VicidialConfig::create() argument, see VicidialConfig::fromArray().
 */
return [
    // Host name or IP of the VICIdial web server, without protocol
    'host' => env('VICIDIAL_HOST', 'localhost'),

    // API user (Admin > Users, user_level 7+ with API access)
    'user' => env('VICIDIAL_USER', ''),
    'pass' => env('VICIDIAL_PASS', ''),

    // http or https
    'protocol' => env('VICIDIAL_PROTOCOL', 'https'),
    'verify_tls' => (bool) env('VICIDIAL_VERIFY_TLS', true),

    // seconds
    'timeout' => (int) env('VICIDIAL_TIMEOUT', 30),
    'connect_timeout' => (int) env('VICIDIAL_CONNECT_TIMEOUT', 10),

    // Free-text label shown in VICIdial logs, max 20 characters
    'source' => env('VICIDIAL_SOURCE', 'vicidial-api-wrapper'),

    // null keeps the wrapper's default User-Agent
    'user_agent' => env('VICIDIAL_USER_AGENT'),

    // HTTP basic auth in front of the API scripts (remote agent setups); leave both null to disable
    'basic_auth' => [
        'user' => env('VICIDIAL_BASIC_AUTH_USER'),
        'pass' => env('VICIDIAL_BASIC_AUTH_PASS'),
    ],

    // Retries. VICIdial has no idempotency keys: a network-error retry of add_lead can insert
    // the lead twice, so keep retry_on_network_errors false for clients that write.
    'retry' => [
        'enabled' => (bool) env('VICIDIAL_RETRY', false),
        'max_attempts' => (int) env('VICIDIAL_RETRY_MAX_ATTEMPTS', 3),
        'initial_delay_ms' => 1000,
        'max_delay_ms' => 10000,
        'multiplier' => 2.0,
        'retry_on_5xx' => true,
        'retry_on_429' => true,
        'retry_on_network_errors' => (bool) env('VICIDIAL_RETRY_ON_NETWORK_ERRORS', false),
    ],

    // true: log every request (passwords redacted) through the application's PSR-3 logger
    'log' => (bool) env('VICIDIAL_LOG', false),
];
