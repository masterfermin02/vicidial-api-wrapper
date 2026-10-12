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

### 1. Release hygiene: done in 4.1.0 / 4.1.1
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

### 4. Transport
- `HttpTransporter::sendRequest()` catches `ClientExceptionInterface` but only Guzzle's `ClientException` carries a response; a PSR-18 client that returns 5xx **without throwing** is never retried. Retry on the response status code, not only on exceptions.
- Add an `Idempotency` note: VICIdial has no idempotency keys, so retrying `add_lead` after a timeout can create duplicates. Default `RetryConfig` should retry only read-only functions, or the docs must say so loudly.
- Expose the raw PSR-7 response (status, headers) on `VicidialResponse` for debugging proxies and load balancers (`webserver` function exists for that reason).

### 5. Developer experience
- Laravel service provider + facade + `config/vicidial.php` (the author's other projects are Laravel; this is the most requested integration for PHP dialer tooling).
- A `vicidial:ping` console command that calls `version` on both endpoints and prints permission errors in plain language.
- Fixture-driven "contract" tests built from the example responses in the official docs, so parsing is validated against real shapes (the fixtures file already exists; extend it per function).
- Publish `docs/VICIDIAL_API_GUIDE.md` content in the README's first screen; most visitors want "how do I click-to-dial" in 10 lines.

### 6. Keep parity automatically
- The official docs change a few times a year (`NON-AGENT_API.txt` was updated 2026-09-25). A scheduled job that downloads both files, diffs the function index against `AdminCoverageTest` / `AgentCoverageTest`, and opens an issue when something new appears would keep this list honest.
