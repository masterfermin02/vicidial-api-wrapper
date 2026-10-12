# Laravel integration

Requires Laravel 11 or 12 (`illuminate/support ^11|^12`). The provider is auto-discovered.

```sh
composer require masterfermin02/vicidial-api-wrapper
php artisan vendor:publish --tag=vicidial-config   # writes config/vicidial.php
```

## Configuration

`config/vicidial.php` reads these environment variables:

| Variable | Default | Notes |
|---|---|---|
| `VICIDIAL_HOST` | `localhost` | host name or IP, no protocol |
| `VICIDIAL_USER` / `VICIDIAL_PASS` | | API user (user_level 7+, API access enabled) |
| `VICIDIAL_PROTOCOL` | `https` | `http` or `https` |
| `VICIDIAL_VERIFY_TLS` | `true` | set `false` only for self-signed test boxes |
| `VICIDIAL_TIMEOUT` / `VICIDIAL_CONNECT_TIMEOUT` | `30` / `10` | seconds |
| `VICIDIAL_SOURCE` | `vicidial-api-wrapper` | label shown in VICIdial logs, max 20 chars |
| `VICIDIAL_USER_AGENT` | wrapper default | |
| `VICIDIAL_BASIC_AUTH_USER` / `VICIDIAL_BASIC_AUTH_PASS` | | HTTP basic auth in front of the API scripts |
| `VICIDIAL_RETRY` | `false` | enable retries (see the idempotency note below) |
| `VICIDIAL_RETRY_MAX_ATTEMPTS` | `3` | |
| `VICIDIAL_RETRY_ON_NETWORK_ERRORS` | `false` | a timeout leaves the outcome unknown; `add_lead` could be inserted twice |
| `VICIDIAL_LOG` | `false` | log every request (passwords redacted) through the app's PSR-3 logger |

Every key maps onto a `VicidialConfig::create()` argument through `VicidialConfig::fromArray()`,
so the same array shape works outside Laravel too.

## Usage

```php
use VicidialApi\Laravel\Facades\Vicidial;
use VicidialApi\Parameters\AddLeadParameters;
use VicidialApi\VicidialClient;

// Facade
$lead = Vicidial::admin()->addLeadResponse(new AddLeadParameters(phoneNumber: '7275551212', listId: 101));

// Dependency injection
final class DialController
{
    public function __construct(private readonly VicidialClient $vicidial) {}

    public function __invoke(string $agent, string $phone)
    {
        return $this->vicidial->agent()->dialResponse($agent, ['phone_number' => $phone])->toArray();
    }
}
```

The container binds `VicidialConfig` and `VicidialClient` as singletons and aliases the client as `vicidial`.
To use your own PSR-18 client (for example Laravel's HTTP client fake in tests, or a client with a proxy),
bind it as `vicidial.http_client` before the client is resolved:

```php
$this->app->instance('vicidial.http_client', new GuzzleHttp\Client(['proxy' => 'http://proxy:3128']));
```

## `php artisan vicidial:ping`

Calls four functions and explains what is wrong in plain language:

```
[OK]   Agent API reachable (version): VICIdial 2.14-890a build 210303-1234
[FAIL] Agent API credentials (webserver): auth USER DOES NOT HAVE PERMISSION TO USE THIS FUNCTION - 6666|webserver|ADMIN
       The API user is authenticated but lacks permission for this function. Raise its user_level (7 for most reads, 8 for lists and campaign data, 9 for user and phone administration), enable "View Reports" for report functions, and check the user group has access to the campaign.
[OK]   Non-Agent API reachable (version): VICIdial 2.14-890a build 210303-1234
[FAIL] Non-Agent API credentials (campaigns_list): campaigns_list USER DOES NOT HAVE PERMISSION TO GET CAMPAIGN INFO - 6666|0
       ...
```

| Probe | Function | What a failure means |
|---|---|---|
| Agent API reachable | `version` (no credentials) | wrong host, protocol, path or proxy |
| Agent API credentials | `webserver` | wrong user/password, missing Agent API permission |
| Non-Agent API reachable | `version` (no credentials) | wrong path to `/vicidial/non_agent_api.php` |
| Non-Agent API credentials | `campaigns_list` | user_level below 8, "view reports" off, no viewable campaigns |

Exit code is 0 when all four pass, 1 otherwise, so it can run in a deployment check.
The same logic is available without Laravel through `VicidialApi\Diagnostics\Ping`:

```php
$report = (new \VicidialApi\Diagnostics\Ping($client))->run();
echo $report->toText();
exit($report->ok() ? 0 : 1);
```

## Testing your application

Bind a `MockPsrClient`-style PSR-18 client as `vicidial.http_client` and queue the responses you expect;
`VicidialResponse::from()` parses the same strings VICIdial sends. The wrapper's own
`tests/Fixtures/official/responses.json` holds every example response from the official docs.

## Idempotency

VICIdial has no idempotency keys. With `VICIDIAL_RETRY=true` the wrapper retries 5xx and 429 answers (the
server rejected the request, nothing was written). Network-error retries are off by default in the Laravel
config because a timeout after `add_lead` reached the server inserts the lead twice; enable
`VICIDIAL_RETRY_ON_NETWORK_ERRORS` only for read-only clients or pass `duplicate_check` to `add_lead`.
