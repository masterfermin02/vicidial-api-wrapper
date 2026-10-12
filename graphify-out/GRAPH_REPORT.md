# Graph Report - vicidial-api-wrapper  (2026-10-12)

## Corpus Check
- 140 files · ~90,403 words
- Verdict: corpus is large enough that graph structure adds value.
- Unclassified: 3 file(s) not represented in the graph (top: .dist 2, (none) 1)

## Summary
- 1778 nodes · 3089 edges · 139 communities (79 shown, 60 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 180 edges (avg confidence: 0.91)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `b281d845`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- VicidialResponse
- AdminContract
- Admin
- README
- Parameter
- VicidialConfig
- Agent
- stage output format
- AgentContract
- RetryConfig
- RecordingTransporter
- BasicAuth
- data_N (status line) vs field_N (raw rows) parsed key naming
- FakeHttpTransporter
- VicidialResponses
- BaseUri
- Automatic Retries
- QueryParams
- Error Model Implementation Summary
- Reliability Features Quick Reference
- RetryStatusCodeTest
- Audit of the wrapper against the official VICIdial API docs (Oct 2026)
- VicidialServiceProviderTest.php
- ContentType
- Migration Guide to v2.0
- Test suite summary (unit + contract tests without a live VICIdial)
- VicidialResponseParsingTest
- MockPsrClient
- Payload
- ResponseContract
- Psr\Http\Message\ResponseInterface
- VicidialException (base)
- Headers
- InvalidArgumentException
- HttpTransporter
- VicidialException
- Release Notes - v2.0
- require
- require-dev
- SUCCESS/ERROR/NOTICE response prefix
- composer.json
- InvalidResponseException
- Response
- scripts
- Psr\Log\LoggerInterface
- Response DTOs (VicidialResponse)
- ArrayAccessible
- Key-value text output (version/webserver)
- --BLANK-- clear-field sentinel
- Implementation Complete: Comprehensive Test Suite
- autoload
- rector.php
- HasResponseHelpers
- HasMetaInformation.php
- Summary of Changes - Configuration Object Implementation
- UpdateLeadParameters
- VicidialServiceProvider.php
- autoload-dev
- add_did (Non-Agent API)
- add_dnc_phone (Non-Agent API)
- add_fpg_phone (Non-Agent API)
- add_phone (Non-Agent API)
- add_user (Non-Agent API)
- st_get_agent_active_lead (Agent API)
- .from
- add_group_alias (Non-Agent API)
- add_phone_alias (Non-Agent API)
- blind_monitor (Non-Agent API)
- call_status_stats (Non-Agent API)
- check_phone_number (Non-Agent API)
- lead_dearchive (Non-Agent API)
- lead_field_info (Non-Agent API)
- server_refresh (Non-Agent API)
- update_campaign (Non-Agent API)
- update_cid_group_entry (Non-Agent API)
- update_phone_alias (Non-Agent API)
- update_user (Non-Agent API)
- call_agent (Agent API)
- calls_in_queue_count (Agent API)
- change_ingroups (Agent API)
- preview_dial_action (Agent API)
- send_dtmf (Agent API)
- send_notification (Agent API)
- set_timer_action (Agent API)
- vm_message (Agent API)
- webphone_url (Agent API)
- VicidialClient
- Method Naming Convention Implementation Summary
- ✅ Implementation Checklist
- Ping
- ✅ Test Suite Implementation - Complete
- Quick Start Guide - VicidialConfig
- Factory
- HttpTransporterTest
- 3. PSR-3 Logger Integration 📝
- Reliability Features Implementation Summary
- TransportExceptionTest.php
- ✨ Major Features
- Vicidial API Wrapper v2.0 - Complete Feature Summary
- 📚 Documentation Suite
- Architecture Decisions
- reliability_features.php
- HttpTransporter.php
- 🚀 Performance Impact
- Files Modified
- Files Created
- Performance Considerations
- Production Recommendations
- Q: investigate how to made money from this project
- 📈 Benefits
- 📊 Summary Statistics
- 📝 Files Modified
- 📁 Files Created
- Usage Examples
- 🎓 What We Learned
- 🎯 Production Recommendations
- 📊 Test Coverage
- Backward Compatibility
- Security Considerations
- CLAUDE.md
- DialParameters
- DispoParameters
- RetryException
- extra
- PingReport
- EndpointCheck
- Parameters
- OfficialResponsesTest
- ApiErrorException
- .switchLead
- Parameters.php
- scrape-official-responses.py

## God Nodes (most connected - your core abstractions)
1. `VicidialResponse` - 227 edges
2. `Admin` - 123 edges
3. `Agent` - 110 edges
4. `Payload` - 96 edges
5. `AgentContract` - 66 edges
6. `AdminContract` - 64 edges
7. `VicidialConfig` - 62 edges
8. `RetryConfig` - 53 edges
9. `QueryParams` - 45 edges
10. `HttpTransporter` - 44 edges

## Surprising Connections (you probably didn't know these)
- `Response (internal transporter value object, ValueObjects\Transporter\Response)` --semantically_similar_to--> `VicidialResponse`  [INFERRED] [semantically similar]
  docs/RESPONSE_DTO.md → src/Responses/VicidialResponse.php
- `TransporterException (legacy name used in older docs)` --semantically_similar_to--> `TransportException`  [INFERRED] [semantically similar]
  docs/RESPONSE_DTO.md → src/Exceptions/TransportException.php
- `TransportException Autoload Fix` --references--> `TransportException`  [INFERRED]
  CHANGELOG.md → src/Exceptions/TransportException.php
- `ra_call_control (Agent API)` --conceptually_related_to--> `RemoteAgent`  [INFERRED]
  docs/agent.md → src/Resources/RemoteAgent.php
- `Migration from v1.x strpos('ERROR:') parsing` --references--> `VicidialResponse`  [INFERRED]
  docs/ERROR_HANDLING.md → src/Responses/VicidialResponse.php

## Import Cycles
- None detected.

## Hyperedges (group relationships)
- **Agent API functions served by /agc/api.php** — docs_vicidial_api_guide_agent_api_endpoint, docs_agent_version, docs_agent_webserver, docs_agent_external_hangup, docs_agent_external_status, docs_agent_logout, docs_agent_external_pause, docs_agent_external_dial, docs_agent_preview_dial_action, docs_agent_external_add_lead, docs_agent_change_ingroups, docs_agent_update_fields, docs_agent_set_timer_action, docs_agent_st_login_log, docs_agent_st_get_agent_active_lead, docs_agent_ra_call_control, docs_agent_send_dtmf, docs_agent_park_call, docs_agent_transfer_conference, docs_agent_recording, docs_agent_webphone_url, docs_agent_call_agent, docs_agent_audio_playback, docs_agent_switch_lead, docs_agent_vm_message, docs_agent_pause_code, docs_agent_calls_in_queue_count, docs_agent_force_fronter_leave_3way, docs_agent_force_fronter_audio_stop, docs_agent_stereo_recording, docs_agent_send_notification, docs_agent_refresh_panel [EXTRACTED 1.00]
- **Non-Agent API functions served by /vicidial/non_agent_api.php** — docs_vicidial_api_guide_non_agent_api_endpoint, docs_admin_version, docs_admin_sounds_list, docs_admin_moh_list, docs_admin_vm_list, docs_admin_blind_monitor, docs_admin_agent_ingroup_info, docs_admin_agent_campaigns, docs_admin_campaigns_list, docs_admin_hopper_list, docs_admin_recording_lookup, docs_admin_did_log_export, docs_admin_phone_number_log, docs_admin_agent_stats_export, docs_admin_user_group_status, docs_admin_in_group_status, docs_admin_agent_status, docs_admin_callid_info, docs_admin_lead_field_info, docs_admin_lead_status_search, docs_admin_ccc_lead_info, docs_admin_lead_callback_info, docs_admin_update_log_entry, docs_admin_add_lead, docs_admin_update_lead, docs_admin_add_user, docs_admin_copy_user, docs_admin_update_user, docs_admin_add_group_alias, docs_admin_add_dnc_phone, docs_admin_add_fpg_phone, docs_admin_add_phone, docs_admin_update_phone, docs_admin_add_phone_alias, docs_admin_update_phone_alias, docs_admin_server_refresh, docs_admin_add_list, docs_admin_update_list, docs_admin_list_info, docs_admin_list_custom_fields, docs_admin_check_phone_number, docs_admin_logged_in_agents, docs_admin_call_status_stats, docs_admin_call_dispo_report, docs_admin_update_campaign, docs_admin_add_did, docs_admin_update_did, docs_admin_update_cid_group_entry, docs_admin_batch_update_lead, docs_admin_agent_ingroup_availability, docs_admin_lead_all_info, docs_admin_lead_search, docs_admin_copy_did, docs_admin_delete_dnc_phone, docs_admin_delete_fpg_phone, docs_admin_hopper_bulk_insert, docs_admin_update_alt_url, docs_admin_update_presets, docs_admin_lead_dearchive, docs_admin_update_remote_agent, docs_admin_user_details, docs_admin_copy_phone [EXTRACTED 1.00]
- **Wrapper exception hierarchy rooted at VicidialException** — docs_error_handling_vicidialexception, docs_error_handling_authenticationexception, src_exceptions_transportexception_transportexception, docs_error_handling_apierrorexception, docs_error_handling_invalidresponseexception, docs_error_handling_unserializableresponse, docs_error_handling_configurationexception, docs_error_handling_retryexception, docs_error_handling_invalidargumentexception, docs_error_handling_errorexception [EXTRACTED 1.00]
- **Legacy factory to configuration-object migration flow** — migration_configuration_object_pattern, migration_legacy_factory_deprecation, src_vicidialapi_vicidialapi_create, src_vicidialapi_vicidialapi_createwithbasicauth, src_vicidialapi_vicidialapi_client, src_vicidialconfig_vicidialconfig_create, src_vicidialconfig_vicidialconfig_createwithbasicauth, src_vicidialclient_vicidialclient_make [INFERRED 0.95]

## Communities (139 total, 60 thin omitted)

### Community 0 - "VicidialResponse"
Cohesion: 0.03
Nodes (6): ArrayAccess + Stringable, immutable (set/unset throw BadMethodCallException), get()/has()/asArray()/asText()/toJson()/toArray() accessors, JSON response parsed as-is, VicidialResponse, Stringable, VicidialResponseTest

### Community 3 - "README"
Cohesion: 0.06
Nodes (17): CHANGELOG, VicidialApi::createWithBasicAuth() Made Static, Keep a Changelog, Semantic Versioning, update_remote_agent (Non-Agent API), 100% Backward Compatible / Opt-in Feature Policy, Legacy Factory Method Deprecation, README (+9 more)

### Community 4 - "Parameter"
Cohesion: 0.07
Nodes (6): StringableContract, Parameter, self, self, ResourceUri, ParameterTest

### Community 5 - "VicidialConfig"
Cohesion: 0.10
Nodes (3): VicidialConfig, VicidialConfigFromArrayTest, VicidialConfigTest

### Community 7 - "stage output format"
Cohesion: 0.10
Nodes (34): agent_ingroup_availability (Non-Agent API), agent_stats_export (Non-Agent API), agent_status (Non-Agent API), batch_update_lead (Non-Agent API), callid_info (Non-Agent API), ccc_lead_info (Non-Agent API), did_log_export (Non-Agent API), hopper_bulk_insert (Non-Agent API) (+26 more)

### Community 9 - "RetryConfig"
Cohesion: 0.08
Nodes (4): ConfigurationException, self, RetryConfig, RetryConfigTest

### Community 10 - "RecordingTransporter"
Cohesion: 0.08
Nodes (9): API Coverage Guard Tests, Full Vicidial Agent and Non-Agent API Parity, ReflectionNamedType, RecordingTransporter, AdminCoverageTest, AgentCoverageTest, AgentEncodingTest, AgentResponseVariantsTest (+1 more)

### Community 11 - "BasicAuth"
Cohesion: 0.10
Nodes (5): ApiKey, self, BasicAuth, self, HeadersTest

### Community 12 - "data_N (status line) vs field_N (raw rows) parsed key naming"
Cohesion: 0.18
Nodes (21): add_lead (Non-Agent API), external_add_lead (Agent API), external_dial (Agent API), external_hangup (Agent API), external_pause (Agent API), external_status (Agent API), Example: pause, hangup, dispo, pauseCode in order, logout (Agent API) (+13 more)

### Community 13 - "FakeHttpTransporter"
Cohesion: 0.13
Nodes (4): FakeHttpTransporter, AdminResponseTest, AgentResponseTest, AgentTest

### Community 15 - "BaseUri"
Cohesion: 0.15
Nodes (6): Double Slash in Request URL Fix, PHPUnit\Framework\TestCase, BaseUri, self, BaseUriTest, PayloadUriTest

### Community 16 - "Automatic Retries"
Cohesion: 0.05
Nodes (40): 1. Always Use Timeouts, 2. Enable Retries for Production, 3. Use Logging in Production, 4. Monitor Your Logs, 5. Circuit Breaker Pattern (Advanced), Automatic Retries, Best Practices, Connect Timeout vs Request Timeout (+32 more)

### Community 17 - "QueryParams"
Cohesion: 0.17
Nodes (3): self, QueryParams, QueryParamsTest

### Community 18 - "Error Model Implementation Summary"
Cohesion: 0.06
Nodes (35): 1. VicidialException (Base), 2. AuthenticationException, 3. TransportException, 4. ApiErrorException, 5. InvalidResponseException, 6. ConfigurationException, 7. RetryException, Backward Compatibility (+27 more)

### Community 19 - "Reliability Features Quick Reference"
Cohesion: 0.04
Nodes (48): Admin API Methods (Non-Agent API), Agent API Methods, Avoid These Patterns, Best Practices for Adding New Methods, Conclusion, Core API Methods, Examples, Examples of Correct Naming: (+40 more)

### Community 20 - "RetryStatusCodeTest"
Cohesion: 0.10
Nodes (4): GuzzleHttp\Exception\ClientException, GuzzleHttp\Exception\ServerException, ServerException, RetryStatusCodeTest

### Community 21 - "Audit of the wrapper against the official VICIdial API docs (Oct 2026)"
Cohesion: 0.09
Nodes (25): update_did (Non-Agent API), audio_playback (Agent API), force_fronter_audio_stop (Agent API), force_fronter_leave_3way (Agent API), refresh_panel (Agent API), switch_lead (Agent API), update_fields (Agent API), Audit of the wrapper against the official VICIdial API docs (Oct 2026) (+17 more)

### Community 22 - "VicidialServiceProviderTest.php"
Cohesion: 0.13
Nodes (7): Illuminate\Config\Repository, Illuminate\Console\Command, Illuminate\Support\Facades\Facade, PingCommand, Vicidial, Symfony\Component\Console\Tester\CommandTester, VicidialServiceProviderTest

### Community 23 - "ContentType"
Cohesion: 0.22
Nodes (5): Http\Discovery\Psr17Factory, Http\Message\MultipartStream\MultipartStreamBuilder, ContentType, Method, self

### Community 24 - "Migration Guide to v2.0"
Cohesion: 0.08
Nodes (28): call_dispo_report (Non-Agent API), function query variable selects the Non-Agent API command, Non-Agent API reference (docs/admin.md), ERROR: auth USER DOES NOT HAVE PERMISSION TO USE THIS FUNCTION, Agent API reference (docs/agent.md), Agent API responses: ERROR or SUCCESS with explanation, Required variables for all Agent API calls (user, pass, agent_user, source, hasSSl), Configuration (+20 more)

### Community 25 - "Test suite summary (unit + contract tests without a live VICIdial)"
Cohesion: 0.19
Nodes (14): Scheduled job diffing official docs against coverage tests, Fixture-driven contract tests from the official example responses, 15 Non-Agent + 3 Agent functions added with ...Response() variants, AGENT_API.txt (official, updated 2025-08-30, 31 functions), Coverage-guard tests (AdminCoverageTest / AgentCoverageTest), NON-AGENT_API.txt (official, updated 2026-09-25, 61 functions), Remote agent script /vicidial/vdremote.php, Authentication header tests (Bearer vs Basic Auth) (+6 more)

### Community 27 - "MockPsrClient"
Cohesion: 0.22
Nodes (4): RetryBehaviorTest, MockPsrClient, self, Throwable

### Community 29 - "ResponseContract"
Cohesion: 0.15
Nodes (3): ArrayAccess, MetaInformationContract, ResponseContract

### Community 30 - "Psr\Http\Message\ResponseInterface"
Cohesion: 0.11
Nodes (10): Generator, GuzzleHttp\Client, IteratorAggregate, Psr\Http\Message\RequestInterface, Psr\Http\Message\ResponseInterface, Psr\Http\Message\StreamInterface, ErrorException, StreamResponse (+2 more)

### Community 31 - "VicidialException (base)"
Cohesion: 0.20
Nodes (14): ApiErrorException, AuthenticationException, ConfigurationException, ErrorException (deprecated, use ApiErrorException), Structured exception hierarchy rooted at VicidialException, InvalidArgumentException (deprecated, use ConfigurationException), InvalidResponseException, Log raw responses for debugging (+6 more)

### Community 32 - "Headers"
Cohesion: 0.16
Nodes (5): GuzzleHttp\Exception\ConnectException, GuzzleHttp\Psr7\Request, GuzzleHttp\Psr7\Response, Headers, self

### Community 33 - "InvalidArgumentException"
Cohesion: 0.14
Nodes (6): InvalidArgumentException, InvalidIpException, Throwable, InvalidUrlException, Throwable, Streamable

### Community 35 - "VicidialException"
Cohesion: 0.29
Nodes (4): Exception, AuthenticationException, Throwable, VicidialException

### Community 36 - "Release Notes - v2.0"
Cohesion: 0.07
Nodes (28): 1. VicidialConfig - Configuration Object, 2. VicidialClient - New Entrypoint, 3. Advanced Configuration Options, 4. Basic Authentication Support, 🔄 Breaking Changes, 🐛 Bug Fixes, 🙏 Credits, 🗑️ Deprecations (+20 more)

### Community 37 - "require"
Cohesion: 0.18
Nodes (11): require, ext-json, graham-campbell/guzzle-factory, php, php-http/discovery, php-http/multipart-stream-builder, psr/http-client, psr/http-client-implementation (+3 more)

### Community 38 - "require-dev"
Cohesion: 0.15
Nodes (13): require-dev, guzzlehttp/guzzle, guzzlehttp/psr7, illuminate/config, illuminate/console, illuminate/support, laravel/pint, phpstan/phpstan (+5 more)

### Community 39 - "SUCCESS/ERROR/NOTICE response prefix"
Cohesion: 0.27
Nodes (11): agent_campaigns (Non-Agent API), agent_ingroup_info (Non-Agent API), campaigns_list (Non-Agent API), user_details (Non-Agent API), ok()/failed()/status() success detection, Pipe-delimited list rows (field_N keys), Status detection rules (SUCCESS / ERROR / NOTICE / informational), VicidialResponse::determineStatus() treats any body without ERROR as success (+3 more)

### Community 40 - "composer.json"
Cohesion: 0.17
Nodes (11): php-http/discovery, authors, config, allow-plugins, description, homepage, keywords, license (+3 more)

### Community 41 - "InvalidResponseException"
Cohesion: 0.25
Nodes (4): JsonException, InvalidResponseException, Throwable, UnserializableResponse

### Community 42 - "Response"
Cohesion: 0.18
Nodes (3): GuzzleHttp\Psr7\Utils, self, Response

### Community 43 - "scripts"
Cohesion: 0.25
Nodes (8): scripts, lint, refactor, test, test:lint, test:refactor, test:types, test:unit

### Community 44 - "Psr\Log\LoggerInterface"
Cohesion: 0.19
Nodes (8): Configuration Object Pattern (VicidialConfig + VicidialClient), Psr\Log\LoggerInterface, Psr\Log\LoggerTrait, Standard vs HTTP Basic Authentication Modes, NullLogger, Stringable, self, self

### Community 47 - "Key-value text output (version/webserver)"
Cohesion: 0.36
Nodes (9): version (Non-Agent API), version (Agent API), webserver (Agent API), Request ID tracking via X-Request-ID header, Multi-line key: value text (webserver), VERSION format: KEY: value pairs separated by pipes, Expose raw PSR-7 response (status, headers) on VicidialResponse, vicidial:ping console command calling version on both endpoints (+1 more)

### Community 48 - "--BLANK-- clear-field sentinel"
Cohesion: 0.33
Nodes (6): add_list (Non-Agent API), update_alt_url (Non-Agent API), update_list (Non-Agent API), update_phone (Non-Agent API), update_presets (Non-Agent API), --BLANK-- clear-field sentinel

### Community 49 - "Implementation Complete: Comprehensive Test Suite"
Cohesion: 0.07
Nodes (26): 1. Test Fixtures (`tests/Fixtures/VicidialResponses.php`), 2. Mock PSR-18 Client (`tests/Mocks/MockPsrClient.php`), 3. Unit Tests - ValueObjects, 4. Response Parsing Tests, 5. Contract Tests, 📦 Files Created, 🔧 Fixed Issues, For CI/CD (+18 more)

### Community 50 - "autoload"
Cohesion: 0.50
Nodes (4): autoload, files, psr-4, VicidialApi\\

### Community 51 - "rector.php"
Cohesion: 0.50
Nodes (3): Rector\CodeQuality\Rector\Class_\InlineConstructorDefaultToPropertyRector, Rector\Config\RectorConfig, Rector\Set\ValueObject\SetList

### Community 54 - "Summary of Changes - Configuration Object Implementation"
Cohesion: 0.08
Nodes (24): 1. `src/VicidialApi.php`, 1. `src/VicidialConfig.php`, 2. `README.md`, 2. `src/VicidialClient.php`, 3. `tests/Resources/AgentTest.php`, 3. `tests/VicidialConfigTest.php`, 4. `example/using_config.php`, 5. `MIGRATION.md` (+16 more)

### Community 55 - "UpdateLeadParameters"
Cohesion: 0.13
Nodes (3): AddLeadParameters, UpdateLeadParameters, ParametersTest

### Community 56 - "VicidialServiceProvider.php"
Cohesion: 0.15
Nodes (7): Illuminate\Container\Container, Illuminate\Contracts\Config\Repository, Illuminate\Contracts\Container\Container, Illuminate\Contracts\Foundation\Application, Illuminate\Support\ServiceProvider, VicidialServiceProvider, TestApplication

### Community 57 - "autoload-dev"
Cohesion: 0.67
Nodes (3): autoload-dev, psr-4, Tests\\

### Community 90 - "VicidialClient"
Cohesion: 0.21
Nodes (10): example1_catchAllErrors(), example2_specificErrorHandling(), example3_configurationErrors(), example4_retryHandling(), example5_productionErrorHandling(), handleMissingAgent(), markLeadAsProcessed(), queueLeadForRetry() (+2 more)

### Community 92 - "Method Naming Convention Implementation Summary"
Cohesion: 0.09
Nodes (22): 1. Code Audit, 2. Documentation Created, 3. README.md Updates, 4. Method Verification, 5. Fixed README Examples, Conclusion, Core Conventions:, Created `../METHOD_NAMING_CONVENTIONS.md` (+14 more)

### Community 93 - "✅ Implementation Checklist"
Cohesion: 0.11
Nodes (17): A. Configuration Object Requirements, B. README Improvements, Client Entrypoint, Configuration Object, Core Implementation, Documentation, Example Files, File Structure (+9 more)

### Community 95 - "✅ Test Suite Implementation - Complete"
Cohesion: 0.12
Nodes (16): Benefits Delivered, Contract Tests (15 tests), Created, Files Created/Modified, Final Test Results, Issue Resolved, Key Insight, Modified (+8 more)

### Community 96 - "Quick Start Guide - VicidialConfig"
Cohesion: 0.13
Nodes (15): 1. Standard Authentication (Most Common), 2. With HTTPS (Recommended for Production), 3. Remote Agent with Basic Auth, Admin API, Agent API, All Configuration Options, Basic Usage, Common Operations (+7 more)

### Community 97 - "Factory"
Cohesion: 0.17
Nodes (6): Closure, Http\Discovery\Psr18Client, Http\Discovery\Psr18ClientDiscovery, Psr\Http\Client\ClientInterface, Factory, self

### Community 99 - "3. PSR-3 Logger Integration 📝"
Cohesion: 0.18
Nodes (11): 1. Configurable Timeouts ⏱️, 2. Automatic Retries with Exponential Backoff 🔄, 3. PSR-3 Logger Integration 📝, Connect Timeout, Features Implemented, HttpTransporter Updates, Logger Support, Request Timeout (+3 more)

### Community 100 - "Reliability Features Implementation Summary"
Cohesion: 0.22
Nodes (9): Date: January 26, 2026, Future Enhancements (v2.1+), Integration Tests (Recommended), Overview, Potential Additions, Reliability Features Implementation Summary, Summary, Testing Strategy (+1 more)

### Community 101 - "TransportExceptionTest.php"
Cohesion: 0.28
Nodes (5): TransportException Autoload Fix, NetworkExceptionInterface, Psr\Http\Client\NetworkExceptionInterface, TransportExceptionTest, VicidialApi\Exceptions\TransporterException

### Community 102 - "✨ Major Features"
Cohesion: 0.25
Nodes (8): 1. Method Naming Conventions ✅, 2. Reliability Features 🛡️, 3. Configuration System 🔧, 4. Response DTOs 📦, A. Configurable Timeouts ⏱️, B. Automatic Retries with Exponential Backoff 🔄, C. PSR-3 Logger Integration 📝, ✨ Major Features

### Community 103 - "Vicidial API Wrapper v2.0 - Complete Feature Summary"
Cohesion: 0.33
Nodes (6): 🔄 Backward Compatibility, ✅ Completion Checklist, 🎉 Conclusion, 🔮 Future Enhancements (v2.1+), 🎉 Overview, Vicidial API Wrapper v2.0 - Complete Feature Summary

### Community 104 - "📚 Documentation Suite"
Cohesion: 0.33
Nodes (6): 📚 Documentation Suite, Examples, Guides, Quick Start, Reference, Technical

### Community 105 - "Architecture Decisions"
Cohesion: 0.33
Nodes (6): Architecture Decisions, Why Automatic Parameter Sanitization?, Why Exponential Backoff with Jitter?, Why PSR-3 Logger?, Why RetryConfig Value Object?, Why Separate Connect and Request Timeouts?

### Community 106 - "reliability_features.php"
Cohesion: 0.40
Nodes (4): Monolog\Formatter\JsonFormatter, Monolog\Handler\RotatingFileHandler, Monolog\Handler\StreamHandler, Monolog\Logger

### Community 107 - "HttpTransporter.php"
Cohesion: 0.33
Nodes (5): TransporterException (legacy name used in older docs), TransportException moved to its own file (PSR-4 autoload fix), GuzzleHttp\Exception\BadResponseException, Psr\Http\Client\ClientExceptionInterface, TransportException

### Community 108 - "🚀 Performance Impact"
Cohesion: 0.40
Nodes (5): CPU, Latency, Memory, Network, 🚀 Performance Impact

### Community 109 - "Files Modified"
Cohesion: 0.40
Nodes (5): Client, Configuration, Documentation, Files Modified, Transport Layer

### Community 110 - "Files Created"
Cohesion: 0.40
Nodes (5): Core Implementation, Documentation, Examples, Files Created, Tests

### Community 111 - "Performance Considerations"
Cohesion: 0.40
Nodes (5): CPU, Latency, Memory, Network, Performance Considerations

### Community 112 - "Production Recommendations"
Cohesion: 0.40
Nodes (5): Logging, Monitoring, Production Recommendations, Retries, Timeouts

### Community 113 - "Q: investigate how to made money from this project"
Cohesion: 0.40
Nodes (4): Answer, Outcome, Q: investigate how to made money from this project, Source Nodes

### Community 114 - "📈 Benefits"
Cohesion: 0.50
Nodes (4): 📈 Benefits, For Developers, For Operations, For Users

### Community 115 - "📊 Summary Statistics"
Cohesion: 0.50
Nodes (4): Code, Documentation, Features, 📊 Summary Statistics

### Community 116 - "📝 Files Modified"
Cohesion: 0.50
Nodes (4): Configuration & Client (3 files), Documentation (1 file), 📝 Files Modified, Transport Layer (1 file)

### Community 117 - "📁 Files Created"
Cohesion: 0.50
Nodes (4): Core Implementation (8 files), Documentation (5 files), Examples (2 files), 📁 Files Created

### Community 118 - "Usage Examples"
Cohesion: 0.50
Nodes (4): Custom Retry Strategy, Minimum Production Setup, Usage Examples, With Logging

### Community 119 - "🎓 What We Learned"
Cohesion: 0.67
Nodes (3): Best Practices Implemented, Pain Points Addressed, 🎓 What We Learned

### Community 120 - "🎯 Production Recommendations"
Cohesion: 0.67
Nodes (3): Checklist for Production, Minimum Setup, 🎯 Production Recommendations

### Community 121 - "📊 Test Coverage"
Cohesion: 0.67
Nodes (3): Integration Tests (Recommended), 📊 Test Coverage, Unit Tests

### Community 122 - "Backward Compatibility"
Cohesion: 0.67
Nodes (3): 100% Backward Compatible ✅, Backward Compatibility, Migration Path

### Community 123 - "Security Considerations"
Cohesion: 0.67
Nodes (3): Log Storage, Security Considerations, Sensitive Data Protection

### Community 127 - "RetryException"
Cohesion: 0.25
Nodes (3): Throwable, RetryException, Throwable

### Community 128 - "extra"
Cohesion: 0.29
Nodes (7): Vicidial, dev-master, extra, branch-alias, laravel, aliases, providers

## Ambiguous Edges - Review These
- `audio_playback (Agent API)` → `switch_lead (Agent API)`  [AMBIGUOUS]
  docs/agent.md · relation: references
- `HTTP 4xx/5xx JSON error structure {error:{message,type,code}}` → `Plain HTTP GET + plain-text API (not REST/JSON)`  [AMBIGUOUS]
  docs/RESPONSE_DTO.md · relation: conceptually_related_to

## Knowledge Gaps
- **408 isolated node(s):** `name`, `description`, `keywords`, `homepage`, `license` (+403 more)
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 716 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **60 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **What is the exact relationship between `audio_playback (Agent API)` and `switch_lead (Agent API)`?**
  _Edge tagged AMBIGUOUS (relation: references) - confidence is low._
- **What is the exact relationship between `HTTP 4xx/5xx JSON error structure {error:{message,type,code}}` and `Plain HTTP GET + plain-text API (not REST/JSON)`?**
  _Edge tagged AMBIGUOUS (relation: conceptually_related_to) - confidence is low._
- **Why does `VicidialResponse` connect `VicidialResponse` to `EndpointCheck`, `README`, `OfficialResponsesTest`, `Agent`, `stage output format`, `AgentContract`, `.switchLead`, `RecordingTransporter`, `data_N (status line) vs field_N (raw rows) parsed key naming`, `FakeHttpTransporter`, `BaseUri`, `Audit of the wrapper against the official VICIdial API docs (Oct 2026)`, `VicidialResponseParsingTest`, `ResponseContract`, `VicidialException (base)`, `Headers`, `InvalidArgumentException`, `HttpTransporter`, `SUCCESS/ERROR/NOTICE response prefix`, `Response`, `Response DTOs (VicidialResponse)`, `ArrayAccessible`, `Key-value text output (version/webserver)`, `HasResponseHelpers`, `UpdateLeadParameters`, `.from`, `.agentOptions`, `Ping`, `HttpTransporter.php`, `DialParameters`, `DispoParameters`, `RetryException`?**
  _High betweenness centrality (0.187) - this node is a cross-community bridge._
- **Why does `README` connect `README` to `Admin`, `VicidialConfig`, `Agent`, `RetryConfig`, `RecordingTransporter`, `Psr\Log\LoggerInterface`, `Audit of the wrapper against the official VICIdial API docs (Oct 2026)`, `Migration Guide to v2.0`, `VicidialClient`, `.agentOptions`, `DialParameters`?**
  _High betweenness centrality (0.148) - this node is a cross-community bridge._
- **Why does `Agent` connect `Agent` to `InvalidArgumentException`, `README`, `VicidialConfig`, `.switchLead`, `AgentContract`, `RecordingTransporter`, `FakeHttpTransporter`, `HasResponseHelpers`, `VicidialServiceProviderTest.php`, `UpdateLeadParameters`, `Migration Guide to v2.0`, `VicidialClient`, `.agentOptions`, `DialParameters`, `DispoParameters`?**
  _High betweenness centrality (0.098) - this node is a cross-community bridge._
- **Are the 24 inferred relationships involving `VicidialResponse` (e.g. with `Migration from v1.x strpos('ERROR:') parsing` and `ArrayAccess + Stringable, immutable (set/unset throw BadMethodCallException)`) actually correct?**
  _`VicidialResponse` has 24 INFERRED edges - model-reasoned connections that need verification._
- **Are the 2 inferred relationships involving `Agent` (e.g. with `Full Vicidial Agent and Non-Agent API Parity` and `Single URL-Encoding Guarantee`) actually correct?**
  _`Agent` has 2 INFERRED edges - model-reasoned connections that need verification._