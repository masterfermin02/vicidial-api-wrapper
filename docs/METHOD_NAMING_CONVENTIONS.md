# Method Naming Conventions

## Overview

This document outlines the naming conventions used in the Vicidial API Wrapper and provides guidance for maintaining consistency across the codebase.

## Naming Standard

All public methods in this library follow **camelCase** naming convention, which is the standard for PHP method names.

### Examples of Correct Naming:
- `updateFields()` - Updates lead fields
- `pauseCode()` - Sets a pause code
- `mohList()` - Gets music on hold list
- `addLead()` - Adds a new lead
- `hangup()` - Hangs up a call

## Method Naming Rules

1. **Use camelCase**: First word lowercase, subsequent words capitalized
   - ✅ `updateFields`, `pauseCode`, `mohList`
   - ❌ `update_fields`, `pause_code`, `moh_list`

2. **Be descriptive**: Method names should clearly indicate their purpose
   - ✅ `updateLogEntry`, `leadFieldInfo`, `agentStatus`
   - ❌ `update`, `info`, `status`

3. **Use consistent terminology**:
   - Use `add` for creating new resources: `addLead()`, `addUser()`, `addPhone()`
   - Use `update` for modifying existing resources: `updateLead()`, `updateUser()`, `updatePhone()`
   - Use `get` or descriptive nouns for retrieval: `version()`, `mohList()`, `agentStatus()`

4. **Abbreviations**: Keep common abbreviations consistent
   - `did` (Direct Inward Dialing)
   - `moh` (Music On Hold)
   - `vm` (Voicemail)
   - `ccc` (Customer Contact Center)
   - `dnc` (Do Not Call)
   - `fpg` (Filter Phone Group)
   - `cid` (Caller ID)

## Core API Methods

### Agent API Methods
All agent methods follow camelCase convention:

- `hangup()` - Hang up the current call
- `dispo()` - Set disposition/status
- `pause()` - Pause the agent
- `pauseCode()` - Set pause code
- `dial()` - Make an external dial
- `updateFields()` - Update lead fields on agent screen
- `addLead()` - Add a lead to manual dial list
- `changeInGroups()` - Change agent's in-groups
- `setTimerAction()` - Set timer action
- `previewDial()` - Control preview dial actions
- `transferConference()` - Transfer to conference
- `parkCall()` - Park the current call
- `callAgent()` - Call another agent
- `switchLead()` - Switch to different lead
- `vmMessage()` - Set voicemail message
- `callsInQueueCount()` - Get queue count
- `audioPlayBack()` - Control audio playback
- `webPhoneUrl()` - Get webphone URL
- `sendDtmf()` - Send DTMF tones
- `raCallControl()` - Remote agent call control
- `stLoginLog()` - Store login log
- `stGetAgentActiveLead()` - Get active lead info
- `forceFronterLeave3way()` - Force fronter to leave 3-way
- `forceFronterAudioStop()` - Force fronter audio stop

### Admin API Methods (Non-Agent API)
All admin methods follow camelCase convention:

- `version()` - Get API version
- `mohList()` - Get music on hold list
- `vmList()` - Get voicemail list
- `blindMonitor()` - Blind monitor functionality
- `agentInGroupInfo()` - Get agent in-group information
- `agentCampaigns()` - Get agent campaigns
- `campaignsList()` - Get campaigns list
- `hopperList()` - Get hopper list
- `recordingLookup()` - Look up recordings
- `didLogExport()` - Export DID log
- `phoneNumberLog()` - Get phone number log
- `agentStatsExport()` - Export agent statistics
- `userGroupStatus()` - Get user group status
- `inGroupStatus()` - Get in-group status
- `agentStatus()` - Get agent status
- `callIdInfo()` - Get call ID information
- `leadFieldInfo()` - Get lead field information
- `leadStatusSearch()` - Search lead status
- `cccLeadInfo()` - Get CCC lead information
- `leadCallbackInfo()` - Get lead callback information
- `updateLogEntry()` - Update log entry
- `addLead()` - Add a new lead
- `updateLead()` - Update a lead
- `addUser()` - Add a new user
- `updateUser()` - Update a user
- `addGroupAlias()` - Add group alias
- `addDncPhone()` - Add DNC phone
- `addFpgPhone()` - Add FPG phone
- `addPhone()` - Add a phone
- `updatePhone()` - Update a phone
- `addPhoneAlias()` - Add phone alias
- `updatePhoneAlias()` - Update phone alias
- `serverRefresh()` - Refresh server
- `addList()` - Add a list
- `updateList()` - Update a list
- `listInfo()` - Get list information
- `listCustomFields()` - Get list custom fields
- `checkPhoneNumber()` - Check phone number
- `loggedInAgents()` - Get logged-in agents
- `callStatusStats()` - Get call status statistics
- `callDispoReport()` - Get call disposition report
- `updateCampaign()` - Update campaign
- `addDid()` - Add DID
- `updateDid()` - Update DID
- `updateCidGroupEntry()` - Update CID group entry
- `copyUser()` - Copy user

### Remote Agent Methods
- `active()` - Set remote agent to active
- `inactive()` - Set remote agent to inactive
- `hangUp()` - Hang up remote agent call (Note: Capital 'U' for historical reasons)
- `transfer()` - Transfer call
- `ingroupTransfer()` - Transfer to in-group

## Response DTO Methods

All Response DTO variants follow the same camelCase convention with a `Response` suffix:

- `updateFieldsResponse()` - Returns `VicidialResponse` DTO
- `dialResponse()` - Returns `VicidialResponse` DTO
- `hangupResponse()` - Returns `VicidialResponse` DTO
- `dispoResponse()` - Returns `VicidialResponse` DTO
- `pauseResponse()` - Returns `VicidialResponse` DTO

See [Response DTO Guide](RESPONSE_DTO.md) for more details.

## Legacy Considerations

### Note on `hangUp()` in RemoteAgent
The `RemoteAgent::hangUp()` method uses a capital 'U' (`hangUp` instead of `hangup`) for historical reasons. While this deviates slightly from strict camelCase, it is kept for backward compatibility. PHP method names are case-insensitive, so `hangUp()`, `hangup()`, and `HANGUP()` all refer to the same method.

### Migration from Legacy Code
If you're migrating from older versions or from direct Vicidial API calls that use snake_case (e.g., `external_dial`, `pause_code`), the wrapper provides camelCase equivalents:

| Vicidial API Function | Wrapper Method |
|-----------------------|----------------|
| `external_dial` | `dial()` |
| `external_hangup` | `hangup()` |
| `external_pause` | `pause()` |
| `pause_code` | `pauseCode()` |
| `external_status` | `dispo()` |
| `update_fields` | `updateFields()` |
| `add_lead` | `addLead()` |
| `moh_list` | `mohList()` |
| `vm_list` | `vmList()` |
| `agent_status` | `agentStatus()` |
| `call_status_stats` | `callStatusStats()` |

## Best Practices for Adding New Methods

When adding new methods to the wrapper:

1. **Follow camelCase**: Always use camelCase for method names
2. **Be consistent**: Use established patterns (add/update/get prefixes)
3. **Document clearly**: Add PHPDoc comments explaining the method's purpose
4. **Update contracts**: Add method signatures to the appropriate contract interface
5. **Add Response DTO variant**: Create a corresponding `*Response()` method
6. **Update this document**: Add the new method to the appropriate section
7. **Write tests**: Ensure the method is properly tested

## Examples

### Good Method Names
```php
// Clear, descriptive, follows camelCase
$client->agent()->updateFields($user, $fields);
$client->admin()->mohList($options);
$client->agent()->pauseCode($user, $code);
```

### Avoid These Patterns
```php
// Don't use snake_case
$client->agent()->update_fields($user, $fields);  // ❌

// Don't use unclear abbreviations
$client->admin()->moh($options);  // ❌ (use mohList)

// Don't use redundant prefixes
$client->agent()->agentPauseCode($user, $code);  // ❌ (already in agent context)
```

## Conclusion

Consistent naming conventions make the API easier to learn and use. By following camelCase throughout the library, we maintain compatibility with PHP standards and provide a clean, predictable interface for developers.
