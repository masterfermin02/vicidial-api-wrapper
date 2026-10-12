# Research notes: VICIdial PHP integrations

Research date: October 12, 2026. Project revision reviewed: `ab2d3da`.

This is the editorial evidence and implementation boundary behind [the article draft](vicidial-php-integrations.md). It is not a claim that complete CRM connectors have been built or that the integrations were verified on a live dialer.

## Method and limits

- Queried the existing graph using vocabulary present in its node labels: agent, callback, campaign, dial, dispo, integration, laravel, lead, recording, report, response, retry. Broad results were truncated, so inspected source, configuration, tests, fixtures, and documentation directly for article claims.
- Reviewed both resources, parameter objects, transport/response behavior, Laravel integration, diagnostics, release notes, and API coverage tests.
- Searched the public VICIdial forum for CRM, lead, call-control, disposition, recording, callback, and recent API discussions. This is a qualitative sample, not an exhaustive forum census or a measure of market demand.
- Forum material spans 2012–2025. Dates below identify the discussion evidence, not a claim of current defects. Did not claim that older Vtiger packages work with current Vtiger.
- Opened current official Non-Agent API documentation (updated 2026-09-25) and Call URL documentation (updated 2022-05-18). Direct browser retrieval of the Agent API document failed, but a separate download succeeded; its header confirms 2025-08-30. Checked its external_dial and external_status definitions against the article. The repository also carries the Agent API reference and official response fixtures.
- Two forum threads below were readable through indexed search excerpts but direct page requests failed. Their limits are stated explicitly.
- No operational VICIdial installation was contacted, no calls placed, and no customer data accessed. No article was published.

## Forum evidence

| Thread | Date/context | What it supports | Editorial handling |
|---|---|---|---|
| [CRM Integration](https://www.vicidial.org/VICIDIALforum/viewtopic.php?f=3&t=33431) | 2014–2016; custom CRM and iframe discussion | Agent-session dependency and a reported browser cross-origin problem | Search excerpts available; direct open failed. Illustrates boundaries, not instructions to bypass browser restrictions. |
| [Vicibox 7.0.4 Integration with VTiger 6.5](https://vicidial.org/VICIDIALforum/viewtopic.php?f=4&t=37309) | May–July 2017 | Users wanted practical integration instructions; discussion separates lead transfer, click-to-call, outcomes, recordings, and identity | Full thread opened. Vendor claims in replies are not independent validation or endorsement. |
| [Some info about VICIDial-GoAutoDial](https://vicidial.org/VICIDIALforum/viewtopic.php?f=7&t=39953) | March–April 2020; Odoo-based CRM | API control of a running agent screen is distinct from replacing that screen | Full thread opened; useful architecture boundary. No reproduction of old product/version advice. |
| [Skip lead](https://www.vicidial.org/VICIDIALforum/viewtopic.php?f=3&t=42299) | June–July 2025; manual-preview behavior on an older build | Real browser-extension integration; preview and skip expectations can diverge from manual-dial behavior | Full thread opened. Does not establish behavior for every VICIdial version. |
| [Custom Recording Path](https://www.vicidial.org/VICIDIALforum/viewtopic.php?f=5&t=37755) | October 2017 | User wanted easier CRM recording access; admin recommended recording_lookup | Search excerpts available; direct open failed. Recording API independently checked against official reference and source. |
| [Dispo Call URL — Simple Variables not Passing](https://www.vicidial.org/VICIDIALforum/viewtopic.php?f=4&t=25868) | September 2012–March 2013 | Missing recording variables and recording-processing timing | Full thread opened. Used to motivate deferred lookup, not to assert the historical bug remains. |
| [Use Non-agent api to dial lead 7 days later](https://www.vicidial.org/VICIDIALforum/viewtopic.php?f=4&t=40107) | May 2020 | Distinguishes scheduled callbacks from delaying lead eligibility | Full thread opened. Read through the resolution: a lead filter, not a claim that an appointment API solves all delayed-dialing requirements. |

No reliable VICIdial-forum evidence was found in this pass for demand specifically for this wrapper's HubSpot or GoHighLevel adapters. The article names CRMs only as possible adapter destinations and does not imply those adapters exist.

## Project capability map

| Workflow | Implemented wrapper surface | Additional work outside this package |
|---|---|---|
| Lead intake and updates | `Admin::addLeadResponse()`, `updateLeadResponse()`, `leadSearchResponse()`, `leadAllInfoResponse()`; `AddLeadParameters`, `UpdateLeadParameters` | Input validation, CRM API client, deduplication ledger, lead-ID mapping, queues, reconciliation |
| CRM click-to-call | `Agent::dialResponse()`, `DialParameters`, other session-control methods | Authorized CRM-to-agent mapping, button/controller, agent session and phone setup |
| CRM outcome synchronization | `leadAllInfoResponse()`, `callIdInfoResponse()` can enrich events; `updateAltUrlResponse()` exposes configuration operations | VICIdial Call URL configuration, authenticated event receiver, durable event records, workers, CRM writes |
| Recording lookup | `Admin::recordingLookupResponse()` and `VicidialResponse::rows()` | Per-call correlation, delayed processing, playback authorization, storage/player if desired |
| Callbacks | `Agent::dispoResponse()` and `DispoParameters`; Admin add/update/callback-info functions | Scheduling policy, timezones, callback-status configuration, calendar API and synchronization |
| Reporting | `agentStatsExportResponse()`, `agentStatusResponse()`, `userGroupStatusResponse()`, `inGroupStatusResponse()` | Scheduling, bounded queries, caching, visualization, downstream exports |
| Laravel application wiring | Auto-discovered provider, facade, client/config container bindings, publishable config, `vicidial:ping` | Controllers, jobs, routes, UI, CRM-specific behavior |

Primary local source locations:

- [Agent resource](../../src/Resources/Agent.php): dial methods around lines 194–239; disposition methods around lines 75–97.
- [Admin resource](../../src/Resources/Admin.php): recording lookup around lines 163–176; statistics export around lines 223–236; lead writes around lines 423–458; raw reporting methods around lines 625–648; lead enrichment around lines 767–806.
- [Response DTO](../../src/Responses/VicidialResponse.php): `fields()` and `rows()` around lines 190–228.
- [Typed parameters](../../src/Parameters/AddLeadParameters.php), [dial options](../../src/Parameters/DialParameters.php), [callback disposition options](../../src/Parameters/DispoParameters.php).
- [Laravel guide](../LARAVEL.md), [provider](../../src/Laravel/VicidialServiceProvider.php), [diagnostics](../../src/Diagnostics/Ping.php).

Note: paths above are relative to `docs/blog/` and are checked before delivery.

## Release and accuracy findings

1. **Version boundary:** README explicitly labels 4.2.0 unreleased; local release tags top out at 4.1.0. The article identifies the reviewed commit and does not claim a default stable install includes Laravel or typed parameters.
2. **Resolved earlier issues:** current source includes CI, typed parameters, Agent response variants, revised response parsing, transport status retries, Laravel wiring, and diagnostics. Earlier conversation findings describing all of these as missing are stale.
3. **Graph limitations:** graph results still included older roadmap descriptions. Used direct source to resolve contradictory/stale nodes.
4. **Method availability:** every Agent function has a response variant according to the dedicated test; not every Admin function does. `callStatusStats()` and `callDispoReport()` remain string methods. Do not invent `callDispoReportResponse()`.
5. **Retries:** documentation in `docs/LARAVEL.md` describes 5xx/429 responses as implying nothing was written. That is too broad, especially through proxies. The article deliberately disables retries for the mixed read/write demo client and treats ambiguous errors as requiring reconciliation. No application code was changed as part of this writing task.
6. **HTTP/body interaction:** current response parsing can prioritize a `SUCCESS:` body over an HTTP error code. The article's helper checks both `failed()` and `statusCode()`.
7. **Callback parameters:** the typed update-lead object does not explicitly declare `campaignId`; its generic `with()` escape hatch or array form may be necessary for required campaign parameters. The main example uses the Agent disposition object and explicitly requires the correct call stage.
8. **Permission scope:** API users need operation-specific permissions and campaign/list access. A passing ping is not full authorization certification.
9. **Transport shape:** the wrapper generates URL query parameters for requests. Keep server-side API calls and credentials out of browser code; consider infrastructure access-log handling too. Password redaction in one logger is not a guarantee about every log.
10. **Public identity:** the draft uses the maintainer identity and contact email declared in `composer.json`. Change the CTA to the blog's preferred contact page before publication if desired.

## Official references and narrow conclusions

- [Non-Agent API](https://www.vicidial.org/docs/NON-AGENT_API.txt): checked lead duplicate options and response field order; recording metadata lookup; callback and reporting parameters. A function existing does not make a complete CRM connector.
- [Agent API](https://www.vicidial.org/docs/AGENT_API.txt): direct downloaded reference checked for session control and callback disposition; repository copy and fixtures also consulted. Browser fetch failures and successful download are recorded above.
- [Call URL features](https://vicidial.org/docs/CALL_URL_FEATURES.txt): events are distinct from SDK requests. Dispo Call URL is server-originated and agent-disposition-specific. Start Call URL and No Agent Call URL have documented manual-call limitations. Do not market these hooks as universal events or as an authenticated delivery system supplied by this package.

## Verification

Existing focused PHPUnit selection: `ParametersTest|VicidialResponseParsingTest|OfficialResponsesTest|AgentCoverageTest|AdminCoverageTest|VicidialServiceProviderTest`.

Result: **794 tests passed, 5,111 assertions** on PHP 8.4.16. No coverage driver was installed. This verifies local behavior covered by those tests, not a deployed integration.

The article's six PHP blocks passed `php -l`. A temporary harness ran their exact code with fake environment values and injected `Tests\\Mocks\\MockPsrClient` after the setup block. All five operations passed: add lead, dial, recording lookup, callback disposition, and agent statistics. Checks covered HTTPS and endpoint selection, API function names, lead-ID extraction, recording/report rows, duplicate/list/DNC flags, dial flags, callback date/type, reporting units, and disabled automatic retries.

The article's response helper also rejected four simulated failure shapes: an API `ERROR:` body, an empty body, an HTML login page, and HTTP 503 carrying a `SUCCESS:` body. Relative Markdown links resolve. No example test used real credentials or a live dialer. The Artisan commands were not executed in a consumer app; the existing Laravel provider/command tests in the selection above cover their package integration.

## Suggested follow-up articles

1. From a website form to VICIdial: a complete lead-import workflow with duplicate reconciliation.
2. Building a CRM call button while keeping VICIdial credentials on the server.
3. Receiving Dispo Call URL events in Laravel and preventing duplicate CRM activities.
4. Retrieving the correct recording for a call, including delayed availability.

These should become separate tutorials only after the application-side workflow and examples are implemented and tested. The overview article does not imply those projects already exist.
