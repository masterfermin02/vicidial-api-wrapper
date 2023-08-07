#!/usr/bin/php
<?php

require_once 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/');
$dotenv->load();

use Vicidial\Api\Wrapper\Agent\Client;

$fields['first_name'] = "John";
$fields['last_name'] = "Doe";
$fields['address1'] = "123 Fake St";

try {
    $agent_api = new Client(getenv('VICIDIAL_ADMIN_URL'), getenv('VICIDIAL_USERL'), getenv('VICIDIAL_PASS'));
    echo $agent_api->updateFields("gabriel", $fields);
} catch (Exception $e) {
    echo 'Exception: ',  $e->getMessage(), "\n";
}
