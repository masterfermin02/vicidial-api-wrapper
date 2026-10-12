# Audit and roadmap (October 2026)

This file records what an audit of the wrapper against the official VICIdial API documents found,
what was fixed, and what is still worth doing. It is meant to be edited: delete items as they land.

## What was fixed in this pass

| Finding | Severity | Fix |
|---|---|---|
| `TransportException` lived in `TransporterException.php`, so PSR-4 could not autoload it. Every network error (timeout, DNS, refused connection) died with `Class "TransportException" not found` instead of the documented exception. | **Critical**: the retry/timeout feature was unusable in production. | Class moved to `TransportException.php`; old name kept as a deprecated `class_alias`. Regression test added. |
| `Agent::switchLead()` sent the lead as `value`; the API only reads `lead_id` / `vendor_lead_code`, so the call always returned `ERROR: switch_lead not valid`. | High | Sends `lead_id`. |
| `Agent` methods called `urlencode()` on values that `http_build_query()` encodes again. `+18005551212` reached VICIdial as `%2B18005551212`; agent users with spaces broke. | High | Removed the inner `urlencode()` (34 places). Regression tests assert single encoding. |
| `VicidialApi::createWithBasicAuth()` was an instance method but every example calls it statically. | High | Made static. |
| Request URLs contained `host//agc/api.php`. | Low | Joined with a single slash. |
| 15 Non-Agent functions and 3 Agent functions from the official docs had no wrapper method. | Medium | Added with `...Response()` variants, contract entries, docs and tests. Coverage-guard tests now enforce parity. |
| README Example 5 used `withUrlEncode(true)`, a method removed in v3.0.0; `urlEncodeDefault` config option is a no-op. | Low | Example rewritten; option marked `@deprecated`. |
| "OpenAI" wording left over from the template the transport layer was copied from. | Trivial | Docblocks fixed. |

## Still open, in priority order

### 1. Release hygiene: done in 4.1.0 and the following commits
- ~~Version drift~~ resolved: 4.1.0 tagged and released, README, CHANGELOG and User-Agent agree.
- ~~No CI~~ resolved: `.github/workflows/ci.yml` runs pint, rector, phpstan and phpunit on PHP 8.2, 8.3 and 8.4 for every push and pull request.
- ~~Root directory clutter~~ resolved: user guides moved to `docs/`, implementation summaries to `docs/archive/`, `docs/response-dto.md` merged into `docs/RESPONSE_DTO.md`.
- ~~Commit or ignore `graphify-out/`~~ resolved: graph committed, machine-specific files ignored.

### 2. API surface consistency: done in 4.2.0
- ~~23 `Agent` methods have no `...Response()` variant~~ resolved: every Agent function now has both variants, enforced by `AgentResponseVariantsTest`. Making `VicidialResponse` the default return type (string variants as `...Raw()`) is deferred to 5.0.
- ~~`dial()` and `updateFields()` throw `\Exception`~~ resolved: they throw `VicidialApi\Exceptions\InvalidArgumentException` (still a `VicidialException`). `dial()` also accepts `lead_id` without `phone_number`, as the API does.
- ~~`updateFields()` allow-list is stale~~ resolved: `Agent::UPDATE_FIELDS_ALLOWED` matches AGENT_API.txt (2025-08-30) including the `formreload`, `scriptreload`, `script2reload`, `emailreload` and `chatreload` triggers.
- ~~Typed parameter objects~~ resolved: `DialParameters`, `DispoParameters`, `AddLeadParameters`, `UpdateLeadParameters` in `VicidialApi\Parameters`; the array overloads remain.
- ~~`source` defaults to `'test'`~~ resolved: defaults to `vicidial-api-wrapper` (`VicidialConfig::DEFAULT_SOURCE`, exactly the 20-character API limit).

### 3. Response parsing: done in 4.2.0
- ~~Empty bodies and HTML login pages read as success~~ resolved: empty bodies, `text/html` (or `<!DOCTYPE`/`<html`) bodies and HTTP status 400+ without a `SUCCESS:` prefix are `error`.
- ~~`stage=csv|tab|json` reports not parsed~~ resolved: multi-line pipe / CSV / tab output becomes `rows()` (keyed by `columns()` when `header=YES` was sent or a header line is detected); `stage=json` is decoded. JSON is tried before the `key: value` branch, which used to swallow it.
- ~~Document `data_N` / `field_N` or expose `fields()`~~ resolved: `fields()` returns the clean positional values, `message()` the text; `data_N` keeps its historical shape for compatibility. Documented in `docs/RESPONSE_DTO.md` and `docs/response-formats.md`.

### 4. Transport: done in 4.2.0
- ~~5xx without an exception never retried; Guzzle 5xx (`ServerException`) never retried either~~ resolved: the transporter retries on the response status code for non-throwing PSR-18 clients, and inspects `BadResponseException` (both `ClientException` and `ServerException`) for Guzzle. `RetryException` is now actually thrown after exhausted retries (it was unreachable before).
- ~~Idempotency note~~ resolved: `RetryConfig` docblock, `docs/RELIABILITY_FEATURES.md` ("Retries are not idempotent"), the quick reference and the README warn that `add_lead` retries can duplicate and show the split read/write client setup.
- ~~Expose raw PSR-7 status and headers~~ resolved: `VicidialResponse::statusCode()`, `headers()`, `header()`.

### 5. Developer experience: done in 4.2.0
- ~~Laravel service provider + facade + `config/vicidial.php`~~ resolved: `VicidialApi\Laravel\VicidialServiceProvider` (auto-discovered), `Vicidial` facade, publishable `config/vicidial.php`, `VicidialConfig::fromArray()` for any framework. See `docs/LARAVEL.md`.
- ~~`vicidial:ping` console command~~ resolved: `php artisan vicidial:ping` and the framework-free `VicidialApi\Diagnostics\Ping` probe `version` + `webserver` on the Agent API and `version` + `campaigns_list` on the Non-Agent API and explain failures (wrong URL, wrong password, user_level, API access, proxy pages, DNS, TLS) in plain language.
- ~~Fixture-driven contract tests from the official examples~~ resolved: `scripts/scrape-official-responses.py` builds `tests/Fixtures/official/responses.json` (722 example responses for 89 functions); `OfficialResponsesTest` parses every one and checks its status, message and fields.
- ~~README first screen~~ resolved: "Click-to-dial in 10 lines" sits above the install section.

### 6. Keep parity automatically: done in 4.2.0
- ~~Scheduled diff of the official function index~~ resolved: `scripts/check-api-parity.php` downloads both docs, extracts the index and lists documented functions without a wrapper method; `.github/workflows/api-parity.yml` runs it every Monday (and on demand) and opens or comments on an issue labelled `api-parity`.

## Deferred to 5.0 (breaking)
- Return `VicidialResponse` from every resource method by default and keep the string variants as `...Raw()`.
- Drop the `urlEncodeDefault` config option, the `TransporterException` alias and the unused `Payload::list/retrieve/upload/cancel/delete` template methods.
- Replace the "body contains ERROR anywhere" heuristic in `VicidialResponse::determineStatus()` with per-function knowledge of which outputs are raw data.
