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

$fields['first_name'] = "John";
$fields['last_name'] = "Doe";
$fields['address1'] = "123 Fake St";

try {
     $agentApi = VicidialApi::create(
        getenv('YOUR_VICIDIAL_IP'),
        getenv('YOUR_VICIDIAL_USER'),
        getenv('YOUR_VICIDIAL_PASSWORD')
     )->agent();
     echo $agentApi->updateFields("gabriel", $fields);
} catch (Exception $e) {
     echo 'Exception: ',  $e->getMessage(), "\n";
}

```

Example 2:  Hangup Call, Dispo it and Pause Agent
```php

<?php

try {
     $agentApi = VicidialApi::create(
        getenv('YOUR_VICIDIAL_IP'),
        getenv('YOUR_VICIDIAL_USER'),
        getenv('YOUR_VICIDIAL_PASSWORD')
     )->agent();
     $agentApi->pause("gabriel", "PAUSE");
     $agentApi->hangup("gabriel");
     $agentApi->dispo("gabriel", ['value' => 'SALE']);
     $agentApi->pauseCode("gabriel", "BREAK");
} catch (Exception $e) {
     echo 'Exception: ',  $e->getMessage(), "\n";
}

```

Example 3: Update fields on agent screen
```php
    <?php
    
    $fields['first_name'] = "John";
    $fields['last_name'] = "Doe";
    $fields['address1'] = "123 Fake St";
    
    try {
         $agentApi = VicidialApi::create(
             getenv('YOUR_VICIDIAL_IP'),
             getenv('YOUR_VICIDIAL_USER'),
             getenv('YOUR_VICIDIAL_PASSWORD')
         )->agent();
         echo $agentApi->updateFields("gabriel", $fields);
    } catch (Exception $e) {
         echo 'Exception: ',  $e->getMessage(), "\n";
    }

```

### Vicidial admin or No agent api
```php
    <?php
    
    require_once 'vendor/autoload.php';
    
    try {
        $agentApi = VicidialApi::create(
            getenv('YOUR_VICIDIAL_IP'),
            getenv('YOUR_VICIDIAL_USER'),
            getenv('YOUR_VICIDIAL_PASSWORD')
        )->admin();
        echo $agentApi->mohList([
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

    $fields['first_name'] = "John";
    $fields['last_name']  = "Doe";
    $fields['address1']   = "123 Fake St";
    $fields['phone_number']   = "18002474747";
    
    try {
        $agentApi = VicidialApi::create(
            getenv('YOUR_VICIDIAL_IP'),
            getenv('YOUR_VICIDIAL_USER'),
            getenv('YOUR_VICIDIAL_PASSWORD'),
            "test",
            false
        )->admin();
        echo $agentApi
        ->withUrlEncode(true)
        ->addLead($fields);
    } catch (Exception $e) {
        echo 'Exception: ', $e->getMessage(), "\n";
    }
```

Login remote agent

```php
    <?php
    
    require_once 'vendor/autoload.php';
    
    try {
        $remoteAgent = VicidialApi::createWithBasicAuth(
            getenv('YOUR_VICIDIAL_IP'),
            getenv('YOUR_VICIDIAL_API_USER'),
            getenv('YOUR_VICIDIAL_API_PASSWORD'),
            getenv('YOUR_VICIDIAL_REMOTE_AGENT'),
            getenv('YOUR_VICIDIAL_REMOTE_PASSWORD'),
        )->remoteAgent();
        $remoteAgent->active(
            getenv('agentId'),
            getenv('confExten'),
        );
        $remoteAgent->hangUp(
            "gabriel",
            [
            'status' => 'SALE',
            ]
]
        );
        $remoteAgent->inActive(
            getenv('agentId'),
            getenv('confExten'),
        );
    } catch (Exception $e) {
        echo 'Exception: ',  $e->getMessage(), "\n";
    }
```


### Docs:
- [Agent](https://github.com/masterfermin02/vicidial-api-wrapper/blob/main/docs/agent.md)
- [Admin](https://github.com/masterfermin02/vicidial-api-wrapper/blob/main/docs/admin.md)
