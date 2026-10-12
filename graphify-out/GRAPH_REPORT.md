# Graph Report - vicidial-api-wrapper  (2026-10-12)

## Corpus Check
- 114 files · ~71,388 words
- Verdict: corpus is large enough that graph structure adds value.
- Unclassified: 3 file(s) not represented in the graph (top: .dist 2, (none) 1)

## Summary
- 1152 nodes · 2377 edges · 92 communities (45 shown, 47 thin omitted)
- Extraction: 86% EXTRACTED · 14% INFERRED · 0% AMBIGUOUS · INFERRED: 339 edges (avg confidence: 0.92)
- Token cost: 165,575 input · 0 output

## Community Hubs (Navigation)
- Response DTO Accessors & Admin Response Variants
- AdminContract Interface
- Admin Resource (Non-Agent API)
- Client Entrypoints & Factory
- Stringable Value Objects
- Example Scripts
- Agent Resource (Agent API)
- Non-Agent Reporting & Lead Functions
- AgentContract Interface
- RetryConfig & ConfigurationException
- Coverage Guard Tests & RecordingTransporter
- Auth Value Objects & Headers
- Core Agent Call Flow Functions
- FakeHttpTransporter Response Tests
- Response Fixtures
- BaseUri & URL Joining
- Changelog & Reliability Docs
- QueryParams Value Object
- Naming Conventions Docs
- TransportException Autoload Fix
- v2.0 Config Migration Docs
- Oct 2026 Audit Findings
- Transporter Contract & RemoteAgent
- Payload & HTTP Enums
- Vicidial API Auth & Error Semantics
- Test Strategy & Official Doc Sources
- HTTP Client Discovery & Guzzle Fakes
- Retry Behavior Contract Tests
- Payload Unit Tests
- ArrayAccess Response Contracts
- StreamResponse & ErrorException
- Exception Hierarchy Docs
- Headers & Transporter Fakes
- Legacy Exceptions & Streamable
- HttpTransporter Retry Loop
- Base Exception Classes
- HttpTransporter Contract Tests
- Runtime Dependencies
- Dev Dependencies
- Response Status Detection
- Composer Metadata
- Invalid Response Exceptions
- Transporter Response Value Object
- Composer Scripts
- PSR-3 NullLogger
- VicidialResponse Helpers
- ArrayAccessible Trait
- Version & Webserver Functions
- List & Preset Update Functions
- RetryException
- PSR-4 Autoload
- Rector Config
- HasResponseHelpers Trait
- HasMetaInformation Trait
- Test Suite Summaries
- API Parity Additions
- Composer Plugin Config
- Test Autoload
- DID Add/Copy Functions
- DNC Phone Functions
- Filter Phone Group Functions
- Phone Add/Copy Functions
- User Add/Copy Functions
- CRM Login Functions
- Static Constructors
- add_group_alias
- add_phone_alias
- blind_monitor
- call_status_stats
- check_phone_number
- lead_dearchive
- lead_field_info
- server_refresh
- update_campaign
- update_cid_group_entry
- update_phone_alias
- update_user
- call_agent
- calls_in_queue_count
- change_ingroups
- preview_dial_action
- send_dtmf
- send_notification
- set_timer_action
- vm_message
- webphone_url

## God Nodes (most connected - your core abstractions)
1. `VicidialResponse` - 133 edges
2. `Admin` - 122 edges
3. `Payload` - 111 edges
4. `Agent` - 70 edges
5. `AdminContract` - 64 edges
6. `VicidialConfig` - 60 edges
7. `RetryConfig` - 54 edges
8. `QueryParams` - 42 edges
9. `HttpTransporter` - 40 edges
10. `BaseUri` - 39 edges

## Surprising Connections (you probably didn't know these)
- `Response (internal transporter value object, ValueObjects\Transporter\Response)` --semantically_similar_to--> `VicidialResponse`  [INFERRED] [semantically similar]
  docs/RESPONSE_DTO.md → src/Responses/VicidialResponse.php
- `TransporterException (legacy name used in older docs)` --semantically_similar_to--> `TransportException`  [INFERRED] [semantically similar]
  docs/RESPONSE_DTO.md → src/Exceptions/TransportException.php
- `Double Slash in Request URL Fix` --references--> `Payload`  [INFERRED]
  CHANGELOG.md → src/ValueObjects/Transporter/Payload.php
- `Laravel service provider + facade + config/vicidial.php` --conceptually_related_to--> `VicidialConfig`  [INFERRED]
  docs/ROADMAP.md → src/VicidialConfig.php
- `Version drift (README v2.0/v4.0, CHANGELOG 2.0.0, User-Agent 2.0, tag v3.0.2, Packagist 4.0.1)` --references--> `VicidialConfig`  [INFERRED]
  docs/ROADMAP.md → src/VicidialConfig.php

## Import Cycles
- None detected.

## Hyperedges (group relationships)
- **Vicidial Exception Hierarchy (all extend VicidialException)** — error_model_implementation_exception_hierarchy, src_exceptions_vicidialexception_vicidialexception, src_exceptions_authenticationexception_authenticationexception, src_exceptions_transportexception_transportexception, src_exceptions_apierrorexception_apierrorexception, src_exceptions_invalidresponseexception_invalidresponseexception, src_exceptions_configurationexception_configurationexception, src_exceptions_retryexception_retryexception, src_exceptions_errorexception_errorexception, src_exceptions_unserializableresponse_unserializableresponse, src_exceptions_invalidargumentexception_invalidargumentexception [EXTRACTED 1.00]
- **v2.0 Reliability Feature Set (timeouts, retries, logging, redaction)** — reliability_features_connect_request_timeout_split, reliability_features_exponential_backoff_with_jitter, reliability_features_retryable_failure_classification, reliability_features_psr3_logger_integration, reliability_features_sensitive_data_redaction, reliability_implementation_retry_loop_flow, src_valueobjects_retryconfig_retryconfig, src_transporters_httptransporter_httptransporter, src_logger_nulllogger_nulllogger, src_vicidialconfig_vicidialconfig [INFERRED 0.95]
- **Legacy factory to configuration-object migration flow** — migration_configuration_object_pattern, migration_legacy_factory_deprecation, src_vicidialapi_vicidialapi_create, src_vicidialapi_vicidialapi_createwithbasicauth, src_vicidialapi_vicidialapi_client, src_vicidialconfig_vicidialconfig_create, src_vicidialconfig_vicidialconfig_createwithbasicauth, src_vicidialclient_vicidialclient_make [INFERRED 0.95]
- **Agent API functions served by /agc/api.php** — docs_vicidial_api_guide_agent_api_endpoint, docs_agent_version, docs_agent_webserver, docs_agent_external_hangup, docs_agent_external_status, docs_agent_logout, docs_agent_external_pause, docs_agent_external_dial, docs_agent_preview_dial_action, docs_agent_external_add_lead, docs_agent_change_ingroups, docs_agent_update_fields, docs_agent_set_timer_action, docs_agent_st_login_log, docs_agent_st_get_agent_active_lead, docs_agent_ra_call_control, docs_agent_send_dtmf, docs_agent_park_call, docs_agent_transfer_conference, docs_agent_recording, docs_agent_webphone_url, docs_agent_call_agent, docs_agent_audio_playback, docs_agent_switch_lead, docs_agent_vm_message, docs_agent_pause_code, docs_agent_calls_in_queue_count, docs_agent_force_fronter_leave_3way, docs_agent_force_fronter_audio_stop, docs_agent_stereo_recording, docs_agent_send_notification, docs_agent_refresh_panel [EXTRACTED 1.00]
- **Non-Agent API functions served by /vicidial/non_agent_api.php** — docs_vicidial_api_guide_non_agent_api_endpoint, docs_admin_version, docs_admin_sounds_list, docs_admin_moh_list, docs_admin_vm_list, docs_admin_blind_monitor, docs_admin_agent_ingroup_info, docs_admin_agent_campaigns, docs_admin_campaigns_list, docs_admin_hopper_list, docs_admin_recording_lookup, docs_admin_did_log_export, docs_admin_phone_number_log, docs_admin_agent_stats_export, docs_admin_user_group_status, docs_admin_in_group_status, docs_admin_agent_status, docs_admin_callid_info, docs_admin_lead_field_info, docs_admin_lead_status_search, docs_admin_ccc_lead_info, docs_admin_lead_callback_info, docs_admin_update_log_entry, docs_admin_add_lead, docs_admin_update_lead, docs_admin_add_user, docs_admin_copy_user, docs_admin_update_user, docs_admin_add_group_alias, docs_admin_add_dnc_phone, docs_admin_add_fpg_phone, docs_admin_add_phone, docs_admin_update_phone, docs_admin_add_phone_alias, docs_admin_update_phone_alias, docs_admin_server_refresh, docs_admin_add_list, docs_admin_update_list, docs_admin_list_info, docs_admin_list_custom_fields, docs_admin_check_phone_number, docs_admin_logged_in_agents, docs_admin_call_status_stats, docs_admin_call_dispo_report, docs_admin_update_campaign, docs_admin_add_did, docs_admin_update_did, docs_admin_update_cid_group_entry, docs_admin_batch_update_lead, docs_admin_agent_ingroup_availability, docs_admin_lead_all_info, docs_admin_lead_search, docs_admin_copy_did, docs_admin_delete_dnc_phone, docs_admin_delete_fpg_phone, docs_admin_hopper_bulk_insert, docs_admin_update_alt_url, docs_admin_update_presets, docs_admin_lead_dearchive, docs_admin_update_remote_agent, docs_admin_user_details, docs_admin_copy_phone [EXTRACTED 1.00]
- **Wrapper exception hierarchy rooted at VicidialException** — docs_error_handling_vicidialexception, docs_error_handling_authenticationexception, src_exceptions_transportexception_transportexception, docs_error_handling_apierrorexception, docs_error_handling_invalidresponseexception, docs_error_handling_unserializableresponse, docs_error_handling_configurationexception, docs_error_handling_retryexception, docs_error_handling_invalidargumentexception, docs_error_handling_errorexception [EXTRACTED 1.00]

## Communities (92 total, 47 thin omitted)

### Community 0 - "Response DTO Accessors & Admin Response Variants"
Cohesion: 0.03
Nodes (9): ArrayAccess + Stringable, immutable (set/unset throw BadMethodCallException), get()/has()/asArray()/asText()/toJson()/toArray() accessors, Request ID tracking via X-Request-ID header, VicidialResponse::from() for test doubles, JSON response parsed as-is, Expose raw PSR-7 response (status, headers) on VicidialResponse, VicidialResponse, Stringable (+1 more)

### Community 3 - "Client Entrypoints & Factory"
Cohesion: 0.07
Nodes (12): VicidialApi::createWithBasicAuth() Made Static, Closure, Psr\Http\Message\RequestInterface, Client, Factory, self, VicidialApi, ClientTest (+4 more)

### Community 4 - "Stringable Value Objects"
Cohesion: 0.07
Nodes (6): StringableContract, Parameter, self, self, ResourceUri, ParameterTest

### Community 5 - "Example Scripts"
Cohesion: 0.08
Nodes (16): example1_catchAllErrors(), example2_specificErrorHandling(), example3_configurationErrors(), example4_retryHandling(), example5_productionErrorHandling(), handleMissingAgent(), markLeadAsProcessed(), queueLeadForRetry() (+8 more)

### Community 6 - "Agent Resource (Agent API)"
Cohesion: 0.08
Nodes (3): Agent::switchLead() Sends lead_id, Agent, Payload

### Community 7 - "Non-Agent Reporting & Lead Functions"
Cohesion: 0.10
Nodes (33): agent_ingroup_availability (Non-Agent API), agent_stats_export (Non-Agent API), agent_status (Non-Agent API), batch_update_lead (Non-Agent API), callid_info (Non-Agent API), ccc_lead_info (Non-Agent API), did_log_export (Non-Agent API), hopper_bulk_insert (Non-Agent API) (+25 more)

### Community 9 - "RetryConfig & ConfigurationException"
Cohesion: 0.09
Nodes (5): ConfigurationException Replaces Generic InvalidArgumentException, RetryConfig Value Object Design, ConfigurationException, RetryConfig, RetryConfigTest

### Community 10 - "Coverage Guard Tests & RecordingTransporter"
Cohesion: 0.10
Nodes (5): Single URL-Encoding Guarantee, RecordingTransporter, AdminCoverageTest, AgentCoverageTest, AgentEncodingTest

### Community 11 - "Auth Value Objects & Headers"
Cohesion: 0.10
Nodes (5): ApiKey, self, BasicAuth, self, HeadersTest

### Community 12 - "Core Agent Call Flow Functions"
Cohesion: 0.16
Nodes (26): add_lead (Non-Agent API), moh_list (Non-Agent API), vm_list (Non-Agent API), external_add_lead (Agent API), external_dial (Agent API), external_hangup (Agent API), external_pause (Agent API), external_status (Agent API) (+18 more)

### Community 13 - "FakeHttpTransporter Response Tests"
Cohesion: 0.13
Nodes (3): FakeHttpTransporter, AdminResponseTest, AgentResponseTest

### Community 15 - "BaseUri & URL Joining"
Cohesion: 0.15
Nodes (6): Double Slash in Request URL Fix, PHPUnit\Framework\TestCase, BaseUri, self, BaseUriTest, PayloadUriTest

### Community 16 - "Changelog & Reliability Docs"
Cohesion: 0.31
Nodes (16): CHANGELOG, Keep a Changelog, Semantic Versioning, v2.0 Complete Feature Summary, Reliability Features Guide, Circuit Breaker Pattern, Separate Connect and Request Timeouts, Exponential Backoff with Jitter (+8 more)

### Community 17 - "QueryParams Value Object"
Cohesion: 0.17
Nodes (3): self, QueryParams, QueryParamsTest

### Community 18 - "Naming Conventions Docs"
Cohesion: 0.22
Nodes (11): API Coverage Guard Tests, Error Model Implementation Summary, Method Naming Conventions, camelCase Method Naming Convention, RemoteAgent::hangUp() Casing Quirk, Vicidial snake_case to Wrapper camelCase Mapping, Consistent Method Prefix Terminology, *Response() DTO Method Variant Pattern (+3 more)

### Community 19 - "TransportException Autoload Fix"
Cohesion: 0.16
Nodes (11): TransportException Autoload Fix, TransporterException (legacy name used in older docs), Retry on response status code, not only on thrown exceptions, TransportException moved to its own file (PSR-4 autoload fix), GuzzleHttp\Exception\ClientException, NetworkExceptionInterface, Psr\Http\Client\ClientExceptionInterface, Psr\Http\Client\NetworkExceptionInterface (+3 more)

### Community 20 - "v2.0 Config Migration Docs"
Cohesion: 0.32
Nodes (13): Implementation Checklist, Deprecated Exception Aliases (removal in v3.0), Configuration Object Implementation Summary, Migration Guide to v2.0, 100% Backward Compatible / Opt-in Feature Policy, Configuration Object Pattern (VicidialConfig + VicidialClient), Legacy Factory Method Deprecation, Quick Start Guide (+5 more)

### Community 21 - "Oct 2026 Audit Findings"
Cohesion: 0.12
Nodes (18): update_did (Non-Agent API), audio_playback (Agent API), force_fronter_audio_stop (Agent API), force_fronter_leave_3way (Agent API), refresh_panel (Agent API), switch_lead (Agent API), Audit of the wrapper against the official VICIdial API docs (Oct 2026), VicidialApi::createWithBasicAuth() made static (+10 more)

### Community 22 - "Transporter Contract & RemoteAgent"
Cohesion: 0.14
Nodes (5): update_remote_agent (Non-Agent API), ra_call_control (Agent API), TransporterContract, Transportable, RemoteAgent

### Community 23 - "Payload & HTTP Enums"
Cohesion: 0.22
Nodes (5): Http\Discovery\Psr17Factory, Http\Message\MultipartStream\MultipartStreamBuilder, ContentType, Method, self

### Community 24 - "Vicidial API Auth & Error Semantics"
Cohesion: 0.19
Nodes (17): call_dispo_report (Non-Agent API), function query variable selects the Non-Agent API command, Non-Agent API reference (docs/admin.md), ERROR: auth USER DOES NOT HAVE PERMISSION TO USE THIS FUNCTION, Agent API reference (docs/agent.md), Agent API responses: ERROR or SUCCESS with explanation, Required variables for all Agent API calls (user, pass, agent_user, source, hasSSl), Docs index (docs/README.md) (+9 more)

### Community 25 - "Test Strategy & Official Doc Sources"
Cohesion: 0.16
Nodes (16): Scheduled job diffing official docs against coverage tests, Fixture-driven contract tests from the official example responses, 15 Non-Agent + 3 Agent functions added with ...Response() variants, No CI: composer test (pint, rector, phpstan max, phpunit) runs nowhere, AGENT_API.txt (official, updated 2025-08-30, 31 functions), Coverage-guard tests (AdminCoverageTest / AgentCoverageTest), NON-AGENT_API.txt (official, updated 2026-09-25, 61 functions), Remote agent script /vicidial/vdremote.php (+8 more)

### Community 26 - "HTTP Client Discovery & Guzzle Fakes"
Cohesion: 0.16
Nodes (6): GuzzleHttp\Client, Http\Discovery\Psr18Client, Http\Discovery\Psr18ClientDiscovery, Psr\Http\Client\ClientInterface, FakeGuzzleClient, FakeGuzzleClientException

### Community 27 - "Retry Behavior Contract Tests"
Cohesion: 0.21
Nodes (6): PSR-18 Clients Do Not Throw on HTTP Error Status, Transport Contract Tests, RetryBehaviorTest, MockPsrClient, self, Throwable

### Community 29 - "ArrayAccess Response Contracts"
Cohesion: 0.15
Nodes (3): ArrayAccess, MetaInformationContract, ResponseContract

### Community 30 - "StreamResponse & ErrorException"
Cohesion: 0.20
Nodes (6): Generator, IteratorAggregate, Psr\Http\Message\ResponseInterface, Psr\Http\Message\StreamInterface, ErrorException, StreamResponse

### Community 31 - "Exception Hierarchy Docs"
Cohesion: 0.20
Nodes (14): ApiErrorException, AuthenticationException, ConfigurationException, ErrorException (deprecated, use ApiErrorException), Structured exception hierarchy rooted at VicidialException, InvalidArgumentException (deprecated, use ConfigurationException), InvalidResponseException, Log raw responses for debugging (+6 more)

### Community 32 - "Headers & Transporter Fakes"
Cohesion: 0.21
Nodes (4): GuzzleHttp\Psr7\Response, GuzzleHttp\Psr7\Utils, Headers, self

### Community 33 - "Legacy Exceptions & Streamable"
Cohesion: 0.15
Nodes (6): InvalidArgumentException, InvalidIpException, Throwable, InvalidUrlException, Throwable, Streamable

### Community 34 - "HttpTransporter Retry Loop"
Cohesion: 0.38
Nodes (4): Response (internal transporter value object, ValueObjects\Transporter\Response), HttpTransporter Retry Loop, HttpTransporter, RequestInterface

### Community 35 - "Base Exception Classes"
Cohesion: 0.19
Nodes (7): Structured Exception Hierarchy, Exception, ApiErrorException, Throwable, AuthenticationException, Throwable, VicidialException

### Community 37 - "Runtime Dependencies"
Cohesion: 0.18
Nodes (11): require, ext-json, graham-campbell/guzzle-factory, php, php-http/discovery, php-http/multipart-stream-builder, psr/http-client, psr/http-client-implementation (+3 more)

### Community 38 - "Dev Dependencies"
Cohesion: 0.18
Nodes (11): require-dev, guzzlehttp/guzzle, guzzlehttp/psr7, laravel/pint, nunomaduro/collision, phpstan/phpstan, phpunit/phpunit, rector/rector (+3 more)

### Community 39 - "Response Status Detection"
Cohesion: 0.27
Nodes (11): agent_campaigns (Non-Agent API), agent_ingroup_info (Non-Agent API), campaigns_list (Non-Agent API), user_details (Non-Agent API), ok()/failed()/status() success detection, Pipe-delimited list rows (field_N keys), Status detection rules (SUCCESS / ERROR / NOTICE / informational), VicidialResponse::determineStatus() treats any body without ERROR as success (+3 more)

### Community 40 - "Composer Metadata"
Cohesion: 0.20
Nodes (9): authors, dev-master, description, extra, branch-alias, homepage, keywords, license (+1 more)

### Community 41 - "Invalid Response Exceptions"
Cohesion: 0.25
Nodes (4): JsonException, InvalidResponseException, Throwable, UnserializableResponse

### Community 43 - "Composer Scripts"
Cohesion: 0.25
Nodes (8): scripts, lint, refactor, test, test:lint, test:refactor, test:types, test:unit

### Community 44 - "PSR-3 NullLogger"
Cohesion: 0.39
Nodes (4): Psr\Log\LoggerInterface, Psr\Log\LoggerTrait, NullLogger, Stringable

### Community 47 - "Version & Webserver Functions"
Cohesion: 0.52
Nodes (7): version (Non-Agent API), version (Agent API), webserver (Agent API), Multi-line key: value text (webserver), VERSION format: KEY: value pairs separated by pipes, vicidial:ping console command calling version on both endpoints, Key-value text output (version/webserver)

### Community 48 - "List & Preset Update Functions"
Cohesion: 0.33
Nodes (6): add_list (Non-Agent API), update_alt_url (Non-Agent API), update_list (Non-Agent API), update_phone (Non-Agent API), update_presets (Non-Agent API), --BLANK-- clear-field sentinel

### Community 50 - "PSR-4 Autoload"
Cohesion: 0.50
Nodes (4): autoload, files, psr-4, VicidialApi\\

### Community 51 - "Rector Config"
Cohesion: 0.50
Nodes (3): Rector\CodeQuality\Rector\Class_\InlineConstructorDefaultToPropertyRector, Rector\Config\RectorConfig, Rector\Set\ValueObject\SetList

### Community 54 - "Test Suite Summaries"
Cohesion: 0.67
Nodes (4): Test Fix Summary, Test Suite Implementation, Offline Deterministic Test Strategy, Value Object Unit Tests

### Community 56 - "Composer Plugin Config"
Cohesion: 0.67
Nodes (3): php-http/discovery, config, allow-plugins

### Community 57 - "Test Autoload"
Cohesion: 0.67
Nodes (3): autoload-dev, psr-4, Tests\\

## Ambiguous Edges - Review These
- `Quick Start Guide` → `ConfigurationException Replaces Generic InvalidArgumentException`  [AMBIGUOUS]
  QUICKSTART.md · relation: conceptually_related_to
- `audio_playback (Agent API)` → `switch_lead (Agent API)`  [AMBIGUOUS]
  docs/agent.md · relation: references
- `Plain HTTP GET + plain-text API (not REST/JSON)` → `HTTP 4xx/5xx JSON error structure {error:{message,type,code}}`  [AMBIGUOUS]
  docs/RESPONSE_DTO.md · relation: conceptually_related_to

## Knowledge Gaps
- **101 isolated node(s):** `name`, `description`, `keywords`, `homepage`, `license` (+96 more)
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 360 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **47 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **What is the exact relationship between `Quick Start Guide` and `ConfigurationException Replaces Generic InvalidArgumentException`?**
  _Edge tagged AMBIGUOUS (relation: conceptually_related_to) - confidence is low._
- **What is the exact relationship between `audio_playback (Agent API)` and `switch_lead (Agent API)`?**
  _Edge tagged AMBIGUOUS (relation: references) - confidence is low._
- **What is the exact relationship between `Plain HTTP GET + plain-text API (not REST/JSON)` and `HTTP 4xx/5xx JSON error structure {error:{message,type,code}}`?**
  _Edge tagged AMBIGUOUS (relation: conceptually_related_to) - confidence is low._
- **Why does `VicidialResponse` connect `Response DTO Accessors & Admin Response Variants` to `AdminContract Interface`, `Agent Resource (Agent API)`, `Non-Agent Reporting & Lead Functions`, `Coverage Guard Tests & RecordingTransporter`, `Core Agent Call Flow Functions`, `FakeHttpTransporter Response Tests`, `Naming Conventions Docs`, `TransportException Autoload Fix`, `Oct 2026 Audit Findings`, `Transporter Contract & RemoteAgent`, `ArrayAccess Response Contracts`, `Exception Hierarchy Docs`, `Headers & Transporter Fakes`, `HttpTransporter Retry Loop`, `Response Status Detection`, `Transporter Response Value Object`, `VicidialResponse Helpers`, `ArrayAccessible Trait`, `Version & Webserver Functions`, `HasResponseHelpers Trait`, `Test Suite Summaries`, `API Parity Additions`, `Static Constructors`?**
  _High betweenness centrality (0.245) - this node is a cross-community bridge._
- **Why does `Payload` connect `Agent Resource (Agent API)` to `Headers & Transporter Fakes`, `AdminContract Interface`, `Admin Resource (Non-Agent API)`, `HttpTransporter Retry Loop`, `Stringable Value Objects`, `HttpTransporter Contract Tests`, `Coverage Guard Tests & RecordingTransporter`, `Transporter Response Value Object`, `FakeHttpTransporter Response Tests`, `BaseUri & URL Joining`, `Naming Conventions Docs`, `TransportException Autoload Fix`, `HasResponseHelpers Trait`, `Transporter Contract & RemoteAgent`, `Payload & HTTP Enums`, `Test Suite Summaries`, `Retry Behavior Contract Tests`, `Payload Unit Tests`?**
  _High betweenness centrality (0.209) - this node is a cross-community bridge._
- **Why does `Admin` connect `Admin Resource (Non-Agent API)` to `Response DTO Accessors & Admin Response Variants`, `AdminContract Interface`, `Client Entrypoints & Factory`, `Coverage Guard Tests & RecordingTransporter`, `FakeHttpTransporter Response Tests`, `Naming Conventions Docs`, `HasResponseHelpers Trait`, `Transporter Contract & RemoteAgent`, `API Parity Additions`, `admin() Accessor`?**
  _High betweenness centrality (0.111) - this node is a cross-community bridge._
- **Are the 27 inferred relationships involving `VicidialResponse` (e.g. with `Migration from v1.x strpos('ERROR:') parsing` and `ArrayAccess + Stringable, immutable (set/unset throw BadMethodCallException)`) actually correct?**
  _`VicidialResponse` has 27 INFERRED edges - model-reasoned connections that need verification._