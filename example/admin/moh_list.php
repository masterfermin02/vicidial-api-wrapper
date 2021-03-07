#!/usr/bin/php
<?php

require_once 'vendor/autoload.php';

use Vicidial\Api\Wrapper\Admin\Client;

try {
    $agent_api = new Client("viciexperts.com/admin_demo/", "12345", "12345");
    echo $agent_api->moh_list([
        'format' => 'selectframe',
        'comments' => 'fieldname',
        'stage' => 'date'
    ]);
} catch (Exception $e) {
    echo 'Exception: ',  $e->getMessage(), "\n";
}
