# Vicidial API Response Formats Guide

## Overview

Based on the official Vicidial API documentation, responses come in several distinct formats. The `VicidialResponse` DTO automatically detects and parses all these formats.

## Common Response Formats

### 1. SUCCESS/ERROR Format (Most Common)

This is the most common format for agent and admin API responses.

**Structure**: `TYPE: message - data|more_data`

**Examples**:
```
SUCCESS: external_hangup function set - 1|6666
SUCCESS: external_pause function set - PAUSE|1232020456|6666
SUCCESS: external_dial function set - 7275551212|6666|1|YES|NO|YES|123456|1232020456|9|TESTING|7275551211|
ERROR: agent_user is not logged in - 6666
ERROR: auth USER DOES NOT HAVE PERMISSION TO USE THIS FUNCTION - 6666|webserver|ADMIN
NOTICE: defined dial_ingroup not found - FAKE_INGROUP
```

**Parsed Structure**:
```php
$response->get('result_type');  // "SUCCESS", "ERROR", or "NOTICE"
$response->get('message');      // Full message after the colon
$response->get('data_0');       // First pipe-delimited field
$response->get('data_1');       // Second pipe-delimited field
// etc.
```

**Usage**:
```php
$response = $client->agent()->hangupResponse('agent123');

if ($response->ok()) {
    // SUCCESS: external_hangup function set - 1|6666
    echo "Success! " . $response->get('message');
} else {
    // ERROR: agent_user is not logged in - 6666
    echo "Error: " . $response->get('message');
}
```

### 2. VERSION Format

Special pipe-delimited format with KEY: value pairs.

**Structure**: `KEY1: value1|KEY2: value2|KEY3: value3`

**Example**:
```
VERSION: 2.0.5-2|BUILD: 90116-1229|DATE: 2009-01-15 14:59:33|EPOCH: 1222020803
```

**Parsed Structure**:
```php
$response->get('version');  // "2.0.5-2"
$response->get('build');    // "90116-1229"
$response->get('date');     // "2009-01-15 14:59:33"
$response->get('epoch');    // "1222020803"
```

**Usage**:
```php
$response = $client->agent()->versionResponse();

echo "Version: " . $response->get('version');
echo "Build: " . $response->get('build');
echo "Date: " . $response->get('date');
```

### 3. Pipe-Delimited Lists

Simple pipe-separated values, commonly used for list exports.

**Structure**: `value1|value2|value3|value4`

**Examples**:
```
user|allowed_campaigns_list|allowed_ingroups_list
1000|TESTCAMP-TEST123-TEST987|TEST_IN-TEST_IN2-TEST_IN3-TEST_IN4-SALESLINE

TESTCAMP|Test Campaign|Y|---ALL---|INBOUND_MAN|1|DOWN|PDROP DROP B NEW

INCALL|M4050908070000012345|12345|TESTCAMP|1|Test Agent|AGENTS|3|LOGIN||7275551212|123456|8600051
```

**Parsed Structure**:
```php
$response->get('field_0');  // First field
$response->get('field_1');  // Second field
$response->get('field_2');  // Third field
// etc.
```

**Usage**:
```php
$response = $client->admin()->agentStatusResponse(['agent_user' => 'agent123']);

// INCALL|M4050908070000012345|12345|TESTCAMP|...
$status = $response->get('field_0');      // "INCALL"
$callId = $response->get('field_1');      // "M4050908070000012345"
$leadId = $response->get('field_2');      // "12345"
$campaign = $response->get('field_3');    // "TESTCAMP"
```

### 4. Multi-Line Key-Value Format

Key-value pairs separated by colons, one per line.

**Structure**:
```
key1: value1
key2: value2
key3: value3
```

**Example (webserver response)**:
```
Webserver Data:
set.timezone: America/New_York
abbr.timezone: EDT
dst.timezone: 1
uname: Linux dev-db 2.6.34.8-0.2-default
host name: dev-db
server name: www.vicidial.org
php version: 5.3.3
```

**Parsed Structure**:
```php
$response->get('set.timezone');   // "America/New_York"
$response->get('abbr.timezone');  // "EDT"
$response->get('dst.timezone');   // "1"
$response->get('php version');    // "5.3.3"
```

**Usage**:
```php
$response = $client->agent()->webserverResponse();

echo "Timezone: " . $response->get('set.timezone');
echo "PHP Version: " . $response->get('php version');
echo "Server: " . $response->get('server name');
```

### 5. CSV / Tab / Pipe Reports (multi-line)

Report and list functions accept `stage=csv|tab|json|pipe` and `header=YES`. Multi-line output is
parsed into rows (4.2.0+).

**Example** (`agent_stats_export`, `stage=csv&header=YES`):
```
user,full_name,user_group,calls,login_time
1029,test agent,ADMIN,1,0:01:52
6666,Admin,ADMIN,5,0:04:11
```

**Parsed Structure**:
```php
$response->columns();              // ["user", "full_name", "user_group", "calls", "login_time"]
$response->rows();                 // [["user" => "1029", "full_name" => "test agent", ...], [...]]
$response->rows()[1]['calls'];     // "5"
$response->get('delimiter');       // ","
```

The header row is used when the request sent `header=YES` (the transport passes that hint along) or when the
first line consists of lowercase identifiers. Without a header, rows are plain lists:
```php
// moh_list: default|default_hold.gsm|on_hold_music.wav  (one class per line)
$response->rows()[0][1];           // "default_hold.gsm"
```

`stage=json` output is `json_decode`d: an object is available through `get()`, a list through `rows()`.

### 6. Failure shapes that are not VICIdial output

A proxy, load balancer or web server in front of VICIdial can answer instead of the API. These are
reported as `error` even though the body has no `ERROR:` prefix:

| Body | `status()` | `message()` |
|---|---|---|
| empty | `error` | `Empty response body` |
| HTML body (starts with a tag: `<!DOCTYPE`, `<html`, `<h1>`, ...) | `error` | `HTML response instead of API output (...)` |
| anything with HTTP status 400+ and no `SUCCESS:` prefix | `error` | depends on body |

`statusCode()`, `headers()` and `header('x-served-by')` expose the HTTP layer for debugging.

## Response Status Detection

`status()` is one of:

- `success`: body starts with `SUCCESS:`, or a raw data payload (version, lists, reports) without `ERROR` in it
- `error`: body starts with `ERROR:`, contains `ERROR`, is empty, starts with an HTML tag, or came with HTTP status 400+
- `notice`: body starts with `NOTICE:`

`ok()` is true only for `success`; `failed()` is its inverse.

## Common Response Examples

### Agent API

```php
// Hangup
// Response: SUCCESS: external_hangup function set - 1|6666
$response = $client->agent()->hangupResponse('agent123');
$response->ok(); // true

// Pause
// Response: SUCCESS: external_pause function set - PAUSE|1232020456|6666
$response = $client->agent()->pauseResponse('agent123', 'PAUSE');
$response->ok(); // true

// Dial
// Response: SUCCESS: external_dial function set - 7275551212|6666|1|YES|NO|YES|123456|...
$response = $client->agent()->dialResponse('agent123', ['phone_number' => '7275551212']);
$response->ok(); // true

// Error
// Response: ERROR: agent_user is not logged in - 6666
$response = $client->agent()->hangupResponse('invalid');
$response->ok(); // false
$response->failed(); // true
```

### Admin API

```php
// Version
// Response: VERSION: 2.14-890a|BUILD: 210303-1234|DATE: 2021-03-03...
$response = $client->admin()->versionResponse();
$response->get('version'); // "2.14-890a"

// Agent Status
// Response: INCALL|M4050908070000012345|12345|TESTCAMP|1|Test Agent|...
$response = $client->admin()->agentStatusResponse(['agent_user' => 'agent123']);
$response->get('field_0'); // "INCALL"

// Add Lead
// Response: SUCCESS: add_lead LEAD HAS BEEN ADDED - 12345|101|9876543210
$response = $client->admin()->addLeadResponse(['phone_number' => '9876543210']);
$response->ok(); // true
```

## Best Practices

### 1. Always Check Status
```php
$response = $client->agent()->hangupResponse('agent123');

if ($response->ok()) {
    // Process success
} else {
    // Handle error
    error_log("API Error: " . $response->raw());
}
```

### 2. Access Parsed Data
```php
// Instead of regex on raw string
$raw = $response->raw();
preg_match('/SUCCESS: .+ - (\d+)\|(\d+)/', $raw, $matches);

// Use parsed accessors
[$status, $epoch, $user] = $response->fields();   // "PAUSE", "1232020456", "6666"
$data1 = $response->get('data_1');                // "1232020456"
```

### 3. Use Defaults for Optional Fields
```php
// Safe access with defaults
$leadId = $response->get('lead_id', 'unknown');
$status = $response->get('field_0', 'N/A');
```

### 4. Log Complete Responses
```php
// Log structured data for debugging
$logger->info('API Call', [
    'status' => $response->status(),
    'raw' => $response->raw(),
    'parsed' => $response->parsed(),
]);
```

## Migration from String Responses

### Before
```php
$result = $client->agent()->hangup('agent123');

// Manual parsing required
if (strpos($result, 'SUCCESS') !== false) {
    echo "Success";
}

// Complex regex
preg_match('/SUCCESS: .+ - (\d+)\|(\d+)/', $result, $matches);
$value1 = $matches[1] ?? null;
```

### After
```php
$response = $client->agent()->hangupResponse('agent123');

// Built-in status check
if ($response->ok()) {
    echo "Success";
}

// Direct access
$value1 = $response->fields()[0];
$type = $response->get('result_type'); // "SUCCESS"
$message = $response->message();
```

## Summary

The VicidialResponse DTO handles all Vicidial API response formats automatically:

✅ **SUCCESS/ERROR format** - Most common, auto-parsed  
✅ **VERSION format** - Special key:value pairs in pipes  
✅ **Pipe-delimited lists** - Numbered field access  
✅ **Multi-line key:value** - Direct key access  
✅ **CSV / tab / pipe reports** - `rows()` keyed by header  
✅ **JSON (`stage=json`)** - decoded  
✅ **Empty / HTML / HTTP-error bodies** - reported as errors, not success  

No manual parsing, regex, or format detection needed - it just works!
