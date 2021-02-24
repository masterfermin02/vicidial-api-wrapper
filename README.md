# Vicidial API PHP WRAPPER

Simple and beautiful integration with vicidial api

DISCLAIMER: VICIdial is a registered trademark of the Vicidial Group which i am not related in anyway.

VICIDIAL is a software suite that is designed to interact with the Asterisk Open-Source PBX Phone system to act as a complete inbound/outbound contact center suite with inbound email support as well.

http://www.vicidial.org/vicidial.php

Vicidial has an AGENT API and NON AGENT API, this classes are intended to make it easier to use in PHP

- http://vicidial.org/docs/NON-AGENT_API.txt
- http://vicidial.org/docs/AGENT_API.txt

## Install
Requires PHP 7.4+

`composer require masterfermin02/vicidial-api-wrapper`

## How to use it
Example 1: Update fields on agent screen
```php 

<?php

use Vicidal\Api\Wrapper\Agent\Client;

$fields['first_name'] = "John";
$fields['last_name'] = "Doe";
$fields['address1'] = "123 Fake St";

try {
     $vicidialAPI = new Client("127.0.0.1", "VicidialAPI", "gabriel", "Sup3rP4ss");
     echo $vicidialAPI->update_fields("gabriel", $fields);
} catch (Exception $e) {
     echo 'Exception: ',  $e->getMessage(), "\n";
}

```

Example 2:  Hangup Call, Dispo it and Pause Agent
```php 

<?php

use Vicidal\Api\Wrapper\Agent\Client;

try {
     $vicidialAPI = new Client("127.0.0.1", "VicidialAPI", "gabriel", "Sup3rP4ss");
     $vicidialAPI->pause("gabriel", "PAUSE");
     $vicidialAPI->hangup("gabriel");
     $vicidialAPI->dispo("gabriel", "SALE");
     $vicidialAPI->pause_code("gabriel", "BREAK");
} catch (Exception $e) {
     echo 'Exception: ',  $e->getMessage(), "\n";
}

```

### Docs:
- [Agent](https://github.com/masterfermin02/vicidial-api-wrapper/blob/main/docs/agent.md)
- [Admin](https://github.com/masterfermin02/vicidial-api-wrapper/blob/main/docs/admin.md)
