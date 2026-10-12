#!/usr/bin/env php
<?php

/**
 * Compare the function index of the official VICIdial API documents with the
 * functions this wrapper implements. Exit code 1 when the docs list a function
 * the wrapper lacks (that is the direction that matters: new upstream functions).
 *
 * Usage:
 *   php scripts/check-api-parity.php                       # downloads both docs
 *   php scripts/check-api-parity.php AGENT.txt NONAGENT.txt # local copies
 *
 * Environment: VICIDIAL_AGENT_DOC_URL / VICIDIAL_NON_AGENT_DOC_URL override the URLs.
 */

declare(strict_types=1);

$agentUrl = getenv('VICIDIAL_AGENT_DOC_URL') ?: 'http://vicidial.org/docs/AGENT_API.txt';
$nonAgentUrl = getenv('VICIDIAL_NON_AGENT_DOC_URL') ?: 'http://vicidial.org/docs/NON-AGENT_API.txt';

$agentDoc = isset($argv[1]) ? file_get_contents($argv[1]) : file_get_contents($agentUrl);
$nonAgentDoc = isset($argv[2]) ? file_get_contents($argv[2]) : file_get_contents($nonAgentUrl);

if ($agentDoc === false || $nonAgentDoc === false || strlen($agentDoc) < 1000 || strlen($nonAgentDoc) < 1000) {
    fwrite(STDERR, "Could not download the official docs (or they are unexpectedly short).\n");
    exit(2);
}

/**
 * The index is the block of "name - description" lines that follows "API functions:".
 *
 * @return list<string>
 */
function documentedFunctions(string $doc): array
{
    $start = strpos($doc, 'API functions:');
    if ($start === false) {
        return [];
    }

    $functions = [];
    foreach (explode("\n", substr($doc, $start)) as $line) {
        $line = rtrim($line);
        if (preg_match('/^([a-z][a-z0-9_]*) - /', $line, $m) === 1) {
            $functions[] = $m[1];
        } elseif ($line === '' && $functions !== []) {
            break;
        }
    }

    return array_values(array_unique($functions));
}

/**
 * Functions mentioned in the docs' "Updated:" header, for the report.
 */
function updatedOn(string $doc): string
{
    return preg_match('/Updated:\s*([0-9-]+)/', $doc, $m) === 1 ? $m[1] : 'unknown';
}

$root = dirname(__DIR__);
$agentSource = (string) file_get_contents($root.'/src/Resources/Agent.php');
$adminSource = (string) file_get_contents($root.'/src/Resources/Admin.php');

preg_match_all("/'([a-z][a-z0-9_]*)'/", $agentSource, $m);
$agentWrapped = array_unique($m[1]);
preg_match_all("/makeApiRequest\\('([a-z][a-z0-9_]*)'/", $adminSource, $m);
$adminWrapped = array_unique($m[1]);

$agentDocumented = documentedFunctions($agentDoc);
$nonAgentDocumented = documentedFunctions($nonAgentDoc);

$agentMissing = array_values(array_diff($agentDocumented, $agentWrapped));
$nonAgentMissing = array_values(array_diff($nonAgentDocumented, $adminWrapped));

printf("Agent API      (AGENT_API.txt updated %s): %d documented, %d missing%s\n", updatedOn($agentDoc), count($agentDocumented), count($agentMissing), $agentMissing ? ': '.implode(', ', $agentMissing) : '');
printf("Non-Agent API  (NON-AGENT_API.txt updated %s): %d documented, %d missing%s\n", updatedOn($nonAgentDoc), count($nonAgentDocumented), count($nonAgentMissing), $nonAgentMissing ? ': '.implode(', ', $nonAgentMissing) : '');

if ($agentDocumented === [] || $nonAgentDocumented === []) {
    fwrite(STDERR, "Could not find the function index in one of the docs; the format may have changed.\n");
    exit(2);
}

exit($agentMissing === [] && $nonAgentMissing === [] ? 0 : 1);
