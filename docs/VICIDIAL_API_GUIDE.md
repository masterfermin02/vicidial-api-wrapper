# How the VICIdial API works (and how this wrapper maps onto it)

VICIdial exposes two plain HTTP endpoints, not a REST or JSON API. Each call is a `GET` request with
query parameters; the body of the response is plain text. This guide condenses the two official
documents so you can use this wrapper without reading 200 KB of text files:

- Agent API: <http://vicidial.org/docs/AGENT_API.txt> (updated 2025-08-30, 31 functions)
- Non-Agent API: <http://vicidial.org/docs/NON-AGENT_API.txt> (updated 2026-09-25, 61 functions)

The wrapper covers **every** function in both documents. The coverage-guard tests in
`tests/Resources/AdminCoverageTest.php` and `tests/Resources/AgentCoverageTest.php` fail if a
documented function is ever missing.

## 1. The two endpoints

| | Agent API | Non-Agent API ("Admin") |
|---|---|---|
| Script | `/agc/api.php` | `/vicidial/non_agent_api.php` |
| Acts on | one **logged-in agent session** (pause, hangup, dispo, dial, transfer, recording, screen refresh) | the **system**: leads, lists, campaigns, users, phones, DIDs, DNC, hopper, reports |
| Wrapper class | `VicidialApi\Resources\Agent` | `VicidialApi\Resources\Admin` |
| Extra required param | `agent_user` (whose session to affect) | varies per function |

A third script, `/vicidial/vdremote.php`, is not part of the public API docs. It toggles **remote
agents** (external phones that receive calls without an agent screen) and needs HTTP Basic Auth on
most installs. The wrapper exposes it as `VicidialApi\Resources\RemoteAgent`.

## 2. Authentication and permissions

Every request carries three query parameters, which the wrapper adds for you from `VicidialConfig`:

| Param | Meaning |
|---|---|
| `user` | a VICIdial user with API access |
| `pass` | that user's password |
| `source` | free-text label (max 20 chars) of what originated the call; appears in VICIdial logs |

There is no token, no session and no OAuth. Credentials travel in the query string on every call,
so **use HTTPS** (`protocol: 'https'` in `VicidialConfig`) whenever the web server allows it.

Permissions are per function and are checked against the `vicidial_users` row of the API user:

- `user_level` must be 7, 8 or 9 depending on the function (reporting functions need 7, anything that
  modifies the system needs 8).
- Feature flags such as "View Reports", "Modify Leads", "Modify Users", "Modify Campaigns",
  "AGC Admin Access", "Delete From DNC Lists", "Modify Remote Agents" and (since the 2025-09 build)
  "Agent API Access" (`vdc_agent_api_access`).
- Some functions also honor user-group restrictions ("allowed campaigns", "allowed lists",
  "allowed function restrictions") and an optional IP allow-list configured in VICIdial.

A denied call does **not** return HTTP 401 or 403. It returns HTTP 200 with a body such as:

```
ERROR: add_lead USER DOES NOT HAVE PERMISSION TO ADD LEADS - 6666|0
```

## 3. Response format

Responses are plain text, usually a single line. Three shapes occur:

1. **Status line** for commands: `SUCCESS: <function> <message> - <pipe|delimited|data>`,
   `ERROR: ...` or `NOTICE: ...` (partial or non-blocking condition).
2. **Raw data** for queries: `agent_status`, `campaigns_list`, `lead_all_info`, `user_details` and
   most reports print rows directly, with **no** `SUCCESS` prefix. Success is "inferred by output".
3. **Key-value text** for `version` and `webserver`:
   `VERSION: 2.14-890a|BUILD: 210303-1234|DATE: 2021-03-03 12:00:00|EPOCH: 1614772800`.

Many reporting functions accept output-shaping parameters:

| Param | Values | Notes |
|---|---|---|
| `stage` | `pipe` (default), `csv`, `tab`, `json`, sometimes `newline` | `moh_list`, `vm_list`, `sounds_list` use `selectframe` instead |
| `header` | `YES` / `NO` | adds a column header row |
| `records` | integer | caps result rows (`lead_search` 1000, `batch_update_lead` 100 by default) |

Multi-value inputs are comma separated (`lead_ids=1,2,3`), lists of statuses are space separated
(`url_statuses=SALE1 SALE2`), and the literal `--BLANK--` clears a field in update functions.

`VicidialApi\Responses\VicidialResponse` parses all three shapes: `ok()` / `failed()` look at the
`SUCCESS:` / `ERROR:` prefix, `get('field_N')` indexes pipe-delimited data, `get('version')` reads
key-value output, and `asText()` always returns the raw body. Because query functions return raw
rows, treat `ok()` as "no `ERROR:` seen", not as "rows were found".

## 4. Hard limits worth knowing

| Function | Limit |
|---|---|
| `recording_lookup`, `did_log_export`, `phone_number_log` | 100,000 rows |
| `agent_stats_export`, `user_group_status`, `in_group_status`, `update_log_entry` | 10,000,000 records scanned |
| `lead_status_search` | 2,000 rows |
| `lead_search` | 1,000 rows (default) |
| `batch_update_lead` | 100 leads (default) |
| `hopper_bulk_insert` | 1,000 lead ids per call |
| `source` | 20 characters |
| `pause_code`, `ra_call_control` status | 6 characters |
| recording `stage` suffix | 14 characters |

The documents mention no rate limit. Heavy report pulls still hurt the database; prefer the
per-function date filters and `records` over paging through everything.

## 5. Typical flows

**Hang up, disposition and pause an agent** (order matters, straight from the Agent API doc):

```php
$agent = $client->agent();
$agent->pause('1000', 'PAUSE');          // external_pause, takes effect after the call
$agent->hangup('1000');                  // external_hangup
$agent->dispo('1000', ['value' => 'A']); // external_status
```

**Click-to-dial from a CRM:**

```php
$response = $client->agent()->dialResponse('1000', [
    'phone_number' => '3125551212',
    'phone_code' => '1',
    'search' => 'YES',   // look up an existing lead first
    'preview' => 'NO',   // dial immediately
]);
```

**Push a lead and queue it:**

```php
$admin = $client->admin();
$lead = $admin->addLeadResponse(['phone_number' => '3125551212', 'list_id' => '101', 'first_name' => 'Jane']);
$leadId = $lead->fields()[2]; // "SUCCESS: add_lead LEAD HAS BEEN ADDED - 3125551212|101|<lead_id>|..." -> phone, list, lead_id
$admin->hopperBulkInsert(['lead_ids' => $leadId, 'hopper_priority' => '50']);
```

**Wallboard data:**

```php
$admin->agentInGroupAvailability(['in_groups' => 'SALES,SUPPORT', 'stage' => 'json']);
$admin->inGroupStatus(['in_groups' => 'SALES,SUPPORT', 'stage' => 'csv', 'header' => 'YES']);
$admin->loggedInAgents(['stage' => 'csv', 'header' => 'YES']);
```

## 6. Method name mapping

The wrapper uses camelCase and drops the `external_` prefix: `external_dial` → `dial()`,
`external_status` → `dispo()`, `external_pause` → `pause()`, `external_hangup` → `hangup()`,
`external_add_lead` → `Agent::addLead()`. Everything else is a direct camelCase of the VICIdial name
(`agent_ingroup_availability` → `agentInGroupAvailability()`). Append `Response` to get a
`VicidialResponse` instead of a string. See `METHOD_NAMING_CONVENTIONS.md` for the full table and
`docs/agent.md` / `docs/admin.md` for every parameter of every function.
