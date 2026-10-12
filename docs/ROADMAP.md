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

### 2. API surface consistency
- 23 `Agent` methods have no `...Response()` variant (`pauseCode`, `logout`, `recording`, `parkCall`, `transferConference`, ...). Either generate the missing pairs or, better, make every method return `VicidialResponse` in the next major and keep the string variants as `...Raw()`.
- `Agent::dial()` and `updateFields()` throw `\Exception`; use `InvalidArgumentException` so callers can distinguish programmer errors from transport errors.
- `updateFields()` hard-codes the allowed field list from an old version of the doc. The current doc also allows `vendor_lead_code`-style custom fields and reload triggers (`formreload`, `scriptreload`, ...). Re-sync the list or drop the client-side check and let VICIdial validate.
- Typed parameter objects for the high-traffic functions (`addLead`, `updateLead`, `dial`, `dispo`) would catch typos like `phone_numer` at compile time. Keep the array overloads.
- `source` defaults to `'test'`; default it to the package name so VICIdial logs identify the caller.

### 3. Response parsing
- `VicidialResponse::determineStatus()` returns `success` for any body without `ERROR`, so an empty body or an HTML login page from a misconfigured proxy reads as success. Treat empty bodies and `text/html` content as errors.
- Reports requested with `stage=csv|tab|json` and `header=YES` should parse into rows keyed by header. Today everything multi-line falls into the `key: value` branch or `result`.
- `ok()` is case-insensitive on `SUCCESS:` but `parseContent()` splits `data_N` only when the message contains a pipe. Document the `data_N` / `field_N` naming or expose `fields(): array` directly.

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
