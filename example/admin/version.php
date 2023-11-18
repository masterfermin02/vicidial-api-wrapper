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
    $agent_api = VicidialApi::create(
        getenv('VICIDIAL_ADMIN_URL'),
        getenv('VICDIAL_USER'),
        getenv('VICDIAL_PASS')
    )->admin();
    echo $agent_api->version();
} catch (Exception $e) {
    echo 'Exception: ',  $e->getMessage(), "\n";
}
