# Vicidial API PHP WRAPPER

Beautiful and simple Implementation to integrate Vicidial API

DISCLAIMER: VICIdial is a registered trademark of the Vicidial Group which i am not related in anyway.

VICIDIAL is a software suite that is designed to interact with the Asterisk Open-Source PBX Phone system to act as a complete inbound/outbound contact center suite with inbound email support as well.

http://www.vicidial.org/vicidial.php

Vicidial has an AGENT API and NON AGENT API, this classes are intended to make it easier to use in PHP

- http://vicidial.org/docs/NON-AGENT_API.txt
- http://vicidial.org/docs/AGENT_API.txt

## Install
Requires PHP 8.1

`composer require masterfermin02/vicidial-api-wrapper`

### For PHP 7.4+ must install this version

`composer require masterfermin02/vicidial-api-wrapper:1.0.3`

## How to use it
Example 1: Update fields on agent screen
```php 

<?php

use Vicidial\Api\Wrapper\Agent\Client;

$fields['first_name'] = "John";
$fields['last_name'] = "Doe";
$fields['address1'] = "123 Fake St";

try {
     $agent_api = new Client("127.0.0.1", "gabriel", "Sup3rP4ss");
     echo $agent_api->update_fields("gabriel", $fields);
} catch (Exception $e) {
     echo 'Exception: ',  $e->getMessage(), "\n";
}

```

Example 2:  Hangup Call, Dispo it and Pause Agent
```php 

<?php

use Vicidial\Api\Wrapper\Agent\Client;

try {
     $agent_api = new Client("127.0.0.1", "gabriel", "Sup3rP4ss");
     $agent_api->pause("gabriel", "PAUSE");
     $agent_api->hangup("gabriel");
     $agent_api->dispo("gabriel", ['value' => 'SALE']);
     $agent_api->pause_code("gabriel", "BREAK");
} catch (Exception $e) {
     echo 'Exception: ',  $e->getMessage(), "\n";
}

```

Example 3: Update fields on agent screen
```php 

<?php

use Vicidial\Api\Wrapper\Agent\Client;

$fields['first_name'] = "John";
$fields['last_name'] = "Doe";
$fields['address1'] = "123 Fake St";

try {
     $agent_api = Client::create("127.0.0.1", "gabriel", "Sup3rP4ss");
     echo $agent_api->update_fields("gabriel", $fields);
} catch (Exception $e) {
     echo 'Exception: ',  $e->getMessage(), "\n";
}

```

### Vicidial admin or No agent api
```php
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
```

Url encode
```php
    <?php
    
    require_once 'vendor/autoload.php';
    
    use Vicidial\Api\Wrapper\Admin\Client;

    $fields['first_name'] = "John";
    $fields['last_name']  = "Doe";
    $fields['address1']   = "123 Fake St";
    $fields['phone_number']   = "18002474747";
    
    try {
        $agent_api = new Client("10.0.0.15", "apiuser", "apipass", "test", false);
        echo $agent_api
        ->withUrlEncode(true)
        ->add_lead($fields);
    } catch (Exception $e) {
        echo 'Exception: ', $e->getMessage(), "\n";
    }
```

### Docs:
- [Agent](https://github.com/masterfermin02/vicidial-api-wrapper/blob/main/docs/agent.md)
- [Admin](https://github.com/masterfermin02/vicidial-api-wrapper/blob/main/docs/admin.md)
