# Maintenance scripts

## `check-api-parity.php`

Downloads `AGENT_API.txt` and `NON-AGENT_API.txt` from vicidial.org, extracts the function
index of each, and lists every documented function that has no wrapper method. Exit code 1
when something is missing, 2 when the docs could not be read.

```sh
php scripts/check-api-parity.php
# or against local copies
php scripts/check-api-parity.php /tmp/AGENT_API.txt /tmp/NON-AGENT_API.txt
```

`.github/workflows/api-parity.yml` runs it every Monday and opens (or updates) an issue
labelled `api-parity` when the docs gained a function. `tests/Resources/AgentCoverageTest`
and `AdminCoverageTest` enforce the same list offline, so after adding the new method also
add it to those tests.

## `scrape-official-responses.py`

Rebuilds `tests/Fixtures/official/responses.json` from the `RESPONSES:` / `Example responses:`
blocks of the two docs. `tests/Contract/OfficialResponsesTest` parses every one of them.

```sh
curl -sL http://vicidial.org/docs/AGENT_API.txt -o /tmp/AGENT_API.txt
curl -sL http://vicidial.org/docs/NON-AGENT_API.txt -o /tmp/NON-AGENT_API.txt
python3 scripts/scrape-official-responses.py /tmp/AGENT_API.txt /tmp/NON-AGENT_API.txt tests/Fixtures/official/responses.json
```
