#!/usr/bin/php
<?php

require_once 'vendor/autoload.php';

use Vicidial\Api\Wrapper\Admin\Client;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

try {
    $agent_api = new Client(getenv('VICIDIAL_ADMIN_URL'), getenv('VICIDIAL_USER'), getenv('VICIDIAL_PASS'));
    echo $agent_api->moh_list([
        'format' => 'selectframe',
        'comments' => 'fieldname',
        'stage' => 'date'
    ]);
} catch (Exception $e) {
    echo 'Exception: ',  $e->getMessage(), "\n";
}
