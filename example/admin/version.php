#!/usr/bin/php
<?php

require_once 'vendor/autoload.php';

use Vicidial\Api\Wrapper\Admin\Client;

$dotenv = Dotenv\Dotenv::createUnsafeImmutable(__DIR__ . '/../');
$dotenv->load();

$fields['first_name'] = "John";
$fields['last_name'] = "Doe";
$fields['address1'] = "123 Fake St";

try {
    $agent_api = new Client(getenv('VICIDIAL_ADMIN_URL'), getenv('VICIDIAL_USER'), getenv('VICIDIAL_PASS'));
    echo $agent_api->version();
} catch (Exception $e) {
    echo 'Exception: ',  $e->getMessage(), "\n";
}
