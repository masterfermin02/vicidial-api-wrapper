#!/usr/bin/php
<?php

require_once 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/../');
$dotenv->load();

try {
    $agent_api = VicidialApi::create(
        getenv('VICIDIAL_ADMIN_URL'),
        getenv('VICIDIAL_USER'),
        getenv('VICIDIAL_PASS')
    )->admin();

    echo $agent_api->mohList([
        'format' => 'selectframe',
        'comments' => 'fieldname',
        'stage' => 'date',
    ]);
} catch (Exception $e) {
    echo 'Exception: ',  $e->getMessage(), "\n";
}
