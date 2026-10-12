# Documentation

## Start here
- [How the VICIdial API works](VICIDIAL_API_GUIDE.md): endpoints, auth, permissions, response formats, limits, typical flows
- [Quick start](QUICKSTART.md)
- [Agent API reference](agent.md): every `/agc/api.php` function with parameters, responses and PHP snippets
- [Non-Agent (Admin) API reference](admin.md): every `/vicidial/non_agent_api.php` function

## Using the wrapper
- [Response DTOs](RESPONSE_DTO.md): `VicidialResponse`, `ok()` / `get()` / `asArray()`, method list, migration from string parsing
- [Response formats](response-formats.md): what VICIdial actually returns and how it is parsed
- [Error handling](ERROR_HANDLING.md): exception hierarchy, transient vs permanent failures
- [Reliability features](RELIABILITY_FEATURES.md) and the [quick reference](RELIABILITY_QUICK_REFERENCE.md): timeouts, retries, PSR-3 logging, redaction
- [Method naming conventions](METHOD_NAMING_CONVENTIONS.md): VICIdial snake_case to wrapper camelCase mapping

## Project
- [Roadmap and audit](ROADMAP.md)
- [Changelog](../CHANGELOG.md) and [migration guide](../MIGRATION.md)
- [archive/](archive/): implementation summaries written while building v2.0 to v4.0, kept for history
