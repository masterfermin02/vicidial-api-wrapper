#!/usr/bin/php
<?php

require_once 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/');
$dotenv->load();

$fields['first_name'] = "John";
$fields['last_name'] = "Doe";
$fields['address1'] = "123 Fake St";

try {
    $agent_api = VicidialApi::create(
        getenv('VICIDIAL_ADMIN_URL'),
        getenv('VICDIAL_USER'),
        getenv('VICDIAL_PASS')
    )->agent();
    echo $agent_api->updateFields("gabriel", $fields);
} catch (Exception $e) {
    echo 'Exception: ',  $e->getMessage(), "\n";
}
