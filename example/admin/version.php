#!/usr/bin/php
<?php

require_once 'vendor/autoload.php';

use Vicidial\Api\Wrapper\Admin\Client;

$fields['first_name'] = "John";
$fields['last_name'] = "Doe";
$fields['address1'] = "123 Fake St";

try {
    $agent_api = new Client("viciexperts.com/admin_demo/", "12345", "12345");
    echo $agent_api->version();
} catch (Exception $e) {
    echo 'Exception: ',  $e->getMessage(), "\n";
}
