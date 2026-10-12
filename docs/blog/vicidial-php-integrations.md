---
title: "Six Practical VICIdial Integrations You Can Build with PHP"
description: "Connect VICIdial to your application with lead imports, CRM click-to-call, call outcomes, recording lookup, callbacks, and reporting."
slug: vicidial-php-integrations
status: draft
---

# Six Practical VICIdial Integrations You Can Build with PHP

A lead arrives in your application. An agent calls it in VICIdial. The outcome needs to appear in your CRM, and a supervisor needs to find the recording later.

Each step is manageable. Keeping the records connected is where an integration becomes useful.

I maintain [vicidial-api-wrapper](https://github.com/masterfermin02/vicidial-api-wrapper), an open-source PHP client for VICIdial. It gives PHP applications methods for working with VICIdial's Agent and Non-Agent APIs, along with configuration, response handling, and optional retries.

For this article, I reviewed the wrapper's source and VICIdial forum discussions about CRM integration. The recurring questions are practical: how to move leads, trigger calls, capture outcomes, and retrieve recordings. A [2017 Vtiger integration discussion](https://vicidial.org/VICIDIALforum/viewtopic.php?f=4&t=37309) is a good example of how many different workflows people mean when they say “CRM integration.”

Here are six workflows you can build, with the boundary between the wrapper and your application made explicit.

> **Version and verification note:** The PHP examples target the repository's development code at commit `ab2d3da`, reviewed on October 12, 2026. Typed parameter objects, `fields()`, `rows()`, and Laravel support are marked **4.2.0 unreleased**. They are not a promise about what the latest stable Composer install contains. Use a checkout containing that commit to reproduce these examples, or a later tagged release containing those features. Examples were checked with mocked HTTP responses; no live CRM or VICIdial deployment was exercised for this article.

## Where the wrapper fits

There are two directions to an integration:

```text
Your application → PHP wrapper → VICIdial Agent / Non-Agent API
VICIdial Call URL → Your application's event receiver → CRM API
```

The first direction creates or updates leads, controls a logged-in agent's session, and requests data. The second handles events coming from VICIdial. Your application implements the receiver, record matching, background jobs, and calls to the CRM's API.

The package does not ship ready-made HubSpot, Salesforce, GoHighLevel, or SuiteCRM connectors. Those are possible destinations when their APIs and your account permissions support the workflow; each needs its own adapter.

For normal Agent API call control, keep the VICIdial agent screen running. A CRM can provide buttons that control that session, but installing a PHP library does not replace its browser session or phone connection. This distinction also appears in the [2020 forum discussion about an Odoo-based CRM](https://vicidial.org/VICIDIALforum/viewtopic.php?f=7&t=39953).

## Prepare a client

The current code requires PHP 8.2 or newer. In a checkout containing the development features above, install its dependencies with `composer install`. For a released version, the package name is `masterfermin02/vicidial-api-wrapper`; check its release notes before using the newer helpers below.

Set `VICIDIAL_HOST`, `VICIDIAL_USER`, and `VICIDIAL_PASS` in the PHP process environment. `getenv()` does not load a `.env` file by itself. Use a hostname without a scheme or path, and an API user authorized for the functions and campaigns you need.

```php
<?php

require 'vendor/autoload.php';

use VicidialApi\Responses\VicidialResponse;
use VicidialApi\ValueObjects\RetryConfig;
use VicidialApi\VicidialClient;
use VicidialApi\VicidialConfig;

$config = VicidialConfig::create(
    host: getenv('VICIDIAL_HOST') ?: throw new RuntimeException('Missing host'),
    user: getenv('VICIDIAL_USER') ?: throw new RuntimeException('Missing API user'),
    pass: getenv('VICIDIAL_PASS') ?: throw new RuntimeException('Missing API password'),
    protocol: 'https',
    verifyTls: true,
    source: 'blog-integration',
    retryConfig: RetryConfig::disabled(),
);

$client = VicidialClient::make($config);

function requireVicidialSuccess(VicidialResponse $response): void
{
    if ($response->failed() || ($response->statusCode() ?? 200) >= 400) {
        throw new RuntimeException('VICIdial rejected the operation; investigate internally.');
    }
}
```

The snippets below reuse this client and helper. They demonstrate individual backend operations, not a web application to expose directly. Your request handler or queue worker must also handle transport exceptions, validate inputs, and authorize the action. Keep diagnostic details in restricted logs rather than returning raw responses to visitors.

## 1. Send leads from a form or CRM into VICIdial

Suppose your website accepts a callback request. After validating the submission, your backend can place it in the appropriate VICIdial list.

```php
use VicidialApi\Parameters\AddLeadParameters;

$response = $client->admin()->addLeadResponse(new AddLeadParameters(
    phoneNumber: '2025550142',
    phoneCode: 1,
    listId: 101,
    firstName: 'Alex',
    lastName: 'Example',
    vendorLeadCode: 'CRM-1042',
    duplicateCheck: 'DUPLIST',
    listExistsCheck: true,
    dncCheck: true,
));

requireVicidialSuccess($response);

// Successful add_lead data: phone number, list ID, lead ID, GMT offset.
$leadId = $response->fields()[2] ?? null;
if ($leadId === null || !ctype_digit($leadId)) {
    throw new RuntimeException('Unexpected add_lead response.');
}

// Persist CRM-1042 ↔ $leadId in your application's integration table.
```

List `101` must exist and belong to the intended campaign. The phone number and customer are fictional; substitute validated data and your own list configuration.

`vendorLeadCode` is a useful external reference, but it does not establish a unique database constraint. Keep your own mapping between the CRM record and the returned VICIdial lead ID. If you need a longer external identifier, store it in your integration table rather than truncating it into a collision.

`DUPLIST` checks the phone number within the list. It is not an event idempotency key. A duplicate rejection needs reconciliation, not an endless retry. These request and response details are documented in the [Non-Agent API reference](https://www.vicidial.org/docs/NON-AGENT_API.txt).

For an existing mapped lead, use `updateLeadResponse()` instead of inserting again. Your application still owns webhook authentication, phone normalization, field mapping, and the rules deciding which leads are eligible for calling.

Creating a lead also does not guarantee that it will be dialed immediately. Campaign configuration, list availability, and agent availability remain relevant.

## 2. Add click-to-call to a CRM record

An agent clicks “Call” in your application. Your backend resolves that user's authorized VICIdial agent and the lead to dial.

```php
use VicidialApi\Parameters\DialParameters;

// Demo values: resolve both from authorized records in your own application.
$agentUser = '1000';
$mappedLeadId = '123456';

$dial = $client->agent()->dialResponse(
    $agentUser,
    new DialParameters(
        leadId: $mappedLeadId,
        phoneCode: 1,
        preview: false,
        focus: false,
    ),
);

requireVicidialSuccess($dial);
```

This invokes `external_dial`. Agent `1000` must have an active session and permission to place manual calls. The lead must exist and be accessible in that operating context. A successful API response means the command was accepted; it does not prove the customer answered. See the [Agent API reference](https://www.vicidial.org/docs/AGENT_API.txt) for the function's parameters and error responses.

Keep API credentials on your backend. The browser should send an authenticated request to your application, and your backend should determine which agent and lead that user may control.

Also test preview workflows separately. In the [2025 “Skip lead” thread](https://www.vicidial.org/VICIDIALforum/viewtopic.php?f=3&t=42299), an operator reported that a browser extension could dial successfully but could not offer the expected skip behavior after manual preview. The discussion is a useful reminder that a wrapper cannot remove VICIdial's session and campaign constraints.

## 3. Write call outcomes back to your CRM

When an agent finishes a call, your CRM may need an activity entry, a follow-up task, or a pipeline update.

Configure a **Dispo Call URL** to send the relevant identifiers and outcome to an endpoint you own. VICIdial's [Call URL documentation](https://vicidial.org/docs/CALL_URL_FEATURES.txt) describes this notification after an agent dispositions a call. Calls that never reach an agent require a separate strategy; the documented No Agent Call URL covers certain automatic and inbound call outcomes.

The application-side flow I would build is:

1. Authenticate the event source and validate the payload.
2. Persist the event before acknowledging receipt.
3. Resolve the CRM record using your lead mapping.
4. Process the CRM update in a background job.
5. Record completion and reconcile failures without creating duplicate activities.

The receiver, queue, and CRM adapter are additional application code. For deduplication, use an event identity appropriate to your deployment: typically the VICIdial instance, a call identifier, and the event type, with an explicit policy for later disposition corrections. A lead ID alone cannot distinguish repeated calls.

The wrapper can enrich an event with `leadAllInfoResponse()` or retrieve call details with `callIdInfoResponse()`. Its `agent()->dispoResponse()` does something different: it sends a disposition command **to** an agent session. It does not subscribe to call events.

## 4. Put recording access beside the CRM activity

A supervisor should be able to open a customer's activity and find the related recording without repeating a phone-number search in another system.

In the [2017 “Custom Recording Path” discussion](https://www.vicidial.org/VICIDIALforum/viewtopic.php?f=5&t=37755), a user described precisely that problem. The administrator pointed to `recording_lookup`.

```php
$recordings = $client->admin()->recordingLookupResponse([
    'lead_id' => '123456',
    'stage' => 'pipe',
    'header' => 'YES',
]);

requireVicidialSuccess($recordings);
$recordingRows = $recordings->rows();

// Each row contains metadata including recording_id and location.
// Match the relevant call and apply your application's access controls.
```

A lead can have multiple calls and recordings. Do not attach the first result to every activity. When you have the call's Asterisk `uniqueid`, the API supports using it alongside another search field; keep its meaning distinct from other call identifiers.

Recordings may also need time to finish processing. The older [discussion about recording variables in Dispo Call URLs](https://www.vicidial.org/VICIDIALforum/viewtopic.php?f=4&t=25868) describes that timing problem. A background lookup with bounded retries and a “recording pending” state is a better design than guessing a filename.

Handle “no recordings found” separately from permission or connection failures. A returned location is metadata, not an authorization grant: protect playback in your application and verify how your recording server grants access. The wrapper does not provide storage, transcription, or a player.

## 5. Connect follow-up workflows to scheduled callbacks

An agent may finish a conversation with an agreed time to call again. The wrapper's `DispoParameters` supports a callback datetime, type, and comments alongside the disposition status.

At the correct disposition stage, after the conversation has ended:

```php
use VicidialApi\Parameters\DispoParameters;

$callback = $client->agent()->dispoResponse('1000', new DispoParameters(
    status: 'CALLBK',
    callbackDatetime: '2026-10-19 14:00:00',
    callbackType: 'USERONLY',
    callbackComments: 'Customer requested a follow-up.',
));

requireVicidialSuccess($callback);
```

This is a separate action from dialing; do not run it immediately after the click-to-call snippet. Replace the example date with the customer's agreed time, convert it according to your VICIdial configuration, and ensure that `CALLBK` is configured as a scheduled-callback status. Verify timezone and daylight-saving behavior in your deployment.

For background workflows, `admin()->addLeadResponse()` and `updateLeadResponse()` expose callback options, while `leadCallbackInfoResponse()` retrieves callback information. Check the function's required campaign and assignment parameters before using it.

A callback appointment and a rule that prevents dialing a lead for seven days are different requirements. The [2020 delayed-calling forum thread](https://www.vicidial.org/VICIDIALforum/viewtopic.php?f=4&t=40107) is a useful example: the discussion ultimately used a lead filter for the requested delay. Define the scheduling behavior first, then choose the mechanism.

Calendar synchronization, reminders, rescheduling, and conflict handling remain your application's responsibilities.

## 6. Feed a dashboard or scheduled report

The wrapper also supports reporting and status queries. For example, retrieve a bounded period of agent statistics:

```php
$report = $client->admin()->agentStatsExportResponse([
    'datetime_start' => '2026-10-11 00:00:00',
    'datetime_end' => '2026-10-11 23:59:59',
    'stage' => 'pipe',
    'header' => 'YES',
    'time_format' => 'S',
]);

requireVicidialSuccess($report);
$agentRows = $report->rows();
```

Your application can store these results for a management dashboard or transform them for another reporting system. It supplies the scheduler, database, charts, and destination-specific client.

For current state, inspect `agentStatusResponse()`, `userGroupStatusResponse()`, and `inGroupStatusResponse()`. Cache and coordinate polling across viewers so opening twenty dashboards does not multiply the same API requests twenty times. These are request/response queries, not a streaming feed.

Not every Admin method has a corresponding `...Response()` method. For example, the current `callStatusStats()` and `callDispoReport()` methods return strings. Check the actual resource method before assuming a suffix exists.

## Where Laravel helps

The unreleased Laravel integration supports Laravel 11 and 12, with package discovery, a service provider, a facade, and container injection. After installing a package revision containing it:

```bash
php artisan vendor:publish --tag=vicidial-config
php artisan vicidial:ping
```

The ping command probes both API endpoints and helps explain connectivity or permission failures. Passing its probes does not establish permission for every operation in your integration.

Laravel queues and scheduled jobs are useful places to put lead synchronization, CRM updates, and reporting. The package supplies the client; you still write those jobs. See the [Laravel guide](https://github.com/masterfermin02/vicidial-api-wrapper/blob/ab2d3da/docs/LARAVEL.md).

## A few decisions that make integrations dependable

**Keep retries deliberate.** The examples disable automatic retries because they include writes. A timeout or a server/proxy error can leave the result uncertain; it does not prove nothing was written. Track incoming events, serialize conflicting operations where needed, and reconcile the remote state before replaying an ambiguous write. Duplicate checks are useful but do not create an exactly-once guarantee.

**Keep business identity explicit.** Store the external CRM ID, VICIdial instance, and lead ID together. Track calls separately. A phone number alone is not a reliable customer or event identity.

**Separate screen behavior from data integration.** A web form or screen pop can present CRM information to an agent, but its authentication and browser embedding behavior need their own design. The [CRM integration forum thread](https://www.vicidial.org/VICIDIALforum/viewtopic.php?f=3&t=33431) includes a concrete cross-origin iframe problem. Do not disable browser protections to make an integration appear to work.

**Test the installation you will support.** Verify API permissions, lead mappings, unsuccessful responses, retries after uncertain outcomes, and the exact call lifecycle. Historical forum posts identify useful failure cases; they are not a compatibility guarantee for your server.

## Start with one complete workflow

My suggested first integration is small: take one qualified lead from your application, create its VICIdial record, store the mapping, and return the final call outcome to the original record.

Once that works reliably, add click-to-call, recording access, or reporting around the same identifiers.

The [library is free and open source](https://github.com/masterfermin02/vicidial-api-wrapper). You can use it in your own application, contribute improvements, and share the workflows you build. If you need help implementing an integration or want to sponsor an improvement to the library, [contact me](mailto:masterfermin02@gmail.com).

*This is an independent project and is not affiliated with the VICIdial Group.*
