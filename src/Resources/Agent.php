<?php

namespace VicidialApi\Resources;

use VicidialApi\Contracts\AgentContract;
use VicidialApi\Exceptions\InvalidArgumentException;
use VicidialApi\Parameters\DialParameters;
use VicidialApi\Parameters\DispoParameters;
use VicidialApi\Responses\VicidialResponse;

/**
 * Agent API (`/agc/api.php`).
 *
 * Every function has two variants: `name()` returns the raw response string and
 * `nameResponse()` returns a parsed VicidialResponse DTO. Both build the same query.
 */
final class Agent implements AgentContract
{
    use Concerns\HasResponseHelpers;
    use Concerns\Transportable;

    public const AGENT_URL = '/agc/api.php';

    /**
     * Fields the Agent API accepts for `update_fields` (AGENT_API.txt, updated 2025-08-30).
     *
     * @var list<string>
     */
    public const UPDATE_FIELDS_ALLOWED = [
        // lead data
        'address1', 'address2', 'address3', 'alt_phone', 'city', 'comments', 'country_code',
        'date_of_birth', 'email', 'first_name', 'gender', 'gmt_offset_now', 'last_name',
        'middle_initial', 'phone_number', 'phone_code', 'postal_code', 'province',
        'security_phrase', 'source_id', 'state', 'title', 'vendor_lead_code', 'rank', 'owner',
        // optional non-lead triggers
        'formreload', 'scriptreload', 'script2reload', 'emailreload', 'chatreload',
    ];

    // -------------------------------------------------------------------------
    // external_hangup
    // -------------------------------------------------------------------------

    /**
     * Hang up the agent's current call (`external_hangup`).
     */
    public function hangup(string $agentUser): string
    {
        return $this->requestContent(self::AGENT_URL, $this->hangupOptions($agentUser));
    }

    public function hangupResponse(string $agentUser): VicidialResponse
    {
        return $this->requestResponse(self::AGENT_URL, $this->hangupOptions($agentUser));
    }

    /** @return array<string, string> */
    private function hangupOptions(string $agentUser): array
    {
        return [
            'agent_user' => trim($agentUser),
            'function' => 'external_hangup',
            'value' => '1',
        ];
    }

    // -------------------------------------------------------------------------
    // external_status
    // -------------------------------------------------------------------------

    /**
     * Set the disposition of the current call (`external_status`).
     *
     * @param  array<string, string>|DispoParameters  $options  `value` (status) plus optional callback_datetime, callback_type, callback_comments, qm_dispo_code
     */
    public function dispo(string $agentUser, array|DispoParameters $options): string
    {
        return $this->requestContent(self::AGENT_URL, $this->dispoOptions($agentUser, $options));
    }

    /**
     * @param  array<string, string>|DispoParameters  $options
     */
    public function dispoResponse(string $agentUser, array|DispoParameters $options): VicidialResponse
    {
        return $this->requestResponse(self::AGENT_URL, $this->dispoOptions($agentUser, $options));
    }

    /**
     * @param  array<string, string>|DispoParameters  $options
     * @return array<string, string>
     */
    private function dispoOptions(string $agentUser, array|DispoParameters $options): array
    {
        return $this->optionsToArray($options) + [
            'agent_user' => trim($agentUser),
            'function' => 'external_status',
        ];
    }

    // -------------------------------------------------------------------------
    // external_pause
    // -------------------------------------------------------------------------

    /**
     * Pause or resume the agent (`external_pause`).
     *
     * @param  string  $status  PAUSE or RESUME
     */
    public function pause(string $agentUser, string $status): string
    {
        return $this->requestContent(self::AGENT_URL, $this->valueOptions($agentUser, 'external_pause', trim($status)));
    }

    public function pauseResponse(string $agentUser, string $status): VicidialResponse
    {
        return $this->requestResponse(self::AGENT_URL, $this->valueOptions($agentUser, 'external_pause', trim($status)));
    }

    // -------------------------------------------------------------------------
    // pause_code
    // -------------------------------------------------------------------------

    /**
     * Set the pause code of a paused agent (`pause_code`).
     */
    public function pauseCode(string $agentUser, string $code): string
    {
        return $this->requestContent(self::AGENT_URL, $this->valueOptions($agentUser, 'pause_code', $code));
    }

    public function pauseCodeResponse(string $agentUser, string $code): VicidialResponse
    {
        return $this->requestResponse(self::AGENT_URL, $this->valueOptions($agentUser, 'pause_code', $code));
    }

    // -------------------------------------------------------------------------
    // webserver / version
    // -------------------------------------------------------------------------

    /**
     * Show web server settings (`webserver`).
     */
    public function webserver(): string
    {
        return $this->requestContent(self::AGENT_URL, ['function' => 'webserver']);
    }

    public function webserverResponse(): VicidialResponse
    {
        return $this->requestResponse(self::AGENT_URL, ['function' => 'webserver']);
    }

    /**
     * Show the API version and build (`version`).
     */
    public function version(): string
    {
        return $this->requestContent(self::AGENT_URL, ['function' => 'version']);
    }

    public function versionResponse(): VicidialResponse
    {
        return $this->requestResponse(self::AGENT_URL, ['function' => 'version']);
    }

    // -------------------------------------------------------------------------
    // logout
    // -------------------------------------------------------------------------

    /**
     * Log the agent out (`logout`).
     */
    public function logout(string $agentUser): string
    {
        return $this->requestContent(self::AGENT_URL, $this->valueOptions($agentUser, 'logout', 'LOGOUT'));
    }

    public function logoutResponse(string $agentUser): VicidialResponse
    {
        return $this->requestResponse(self::AGENT_URL, $this->valueOptions($agentUser, 'logout', 'LOGOUT'));
    }

    // -------------------------------------------------------------------------
    // external_dial
    // -------------------------------------------------------------------------

    /**
     * Place a manual dial call on the agent screen (`external_dial`).
     *
     * @param  array<string, string>|DialParameters  $options  `phone_number` or `lead_id` is required; search, preview and focus default to YES, NO, YES
     *
     * @throws InvalidArgumentException when neither phone_number nor lead_id is given
     */
    public function dial(string $agentUser, array|DialParameters $options): string
    {
        return $this->requestContent(self::AGENT_URL, $this->dialOptions($agentUser, $options));
    }

    /**
     * @param  array<string, string>|DialParameters  $options
     *
     * @throws InvalidArgumentException when neither phone_number nor lead_id is given
     */
    public function dialResponse(string $agentUser, array|DialParameters $options): VicidialResponse
    {
        return $this->requestResponse(self::AGENT_URL, $this->dialOptions($agentUser, $options));
    }

    /**
     * @param  array<string, string>|DialParameters  $options
     * @return array<string, string>
     */
    private function dialOptions(string $agentUser, array|DialParameters $options): array
    {
        $options = $this->optionsToArray($options);

        $phoneNumber = trim($options['phone_number'] ?? '');

        if ($phoneNumber === '' && trim($options['lead_id'] ?? '') === '') {
            throw new InvalidArgumentException('dial() needs a phone_number or a lead_id');
        }

        $defaults = [
            'agent_user' => trim($agentUser),
            'function' => 'external_dial',
            'phone_code' => trim($options['phone_code'] ?? ''),
            'search' => 'YES',
            'preview' => 'NO',
            'focus' => 'YES',
        ];

        if ($phoneNumber !== '') {
            $defaults['value'] = $phoneNumber;
        }

        return $options + $defaults;
    }

    // -------------------------------------------------------------------------
    // preview_dial_action
    // -------------------------------------------------------------------------

    /**
     * Send SKIP, DIALONLY, ALTDIAL, ADR3DIAL or FINISH while a lead is previewed (`preview_dial_action`).
     */
    public function previewDial(string $agentUser, string $value): string
    {
        return $this->requestContent(self::AGENT_URL, $this->valueOptions($agentUser, 'preview_dial_action', trim($value)));
    }

    public function previewDialResponse(string $agentUser, string $value): VicidialResponse
    {
        return $this->requestResponse(self::AGENT_URL, $this->valueOptions($agentUser, 'preview_dial_action', trim($value)));
    }

    // -------------------------------------------------------------------------
    // external_add_lead
    // -------------------------------------------------------------------------

    /**
     * Add a lead to the manual dial list of the agent's campaign (`external_add_lead`).
     *
     * @param  array<string, string>  $options
     */
    public function addLead(string $agentUser, array $options): string
    {
        return $this->requestContent(self::AGENT_URL, $this->agentOptions($agentUser, 'external_add_lead', $options));
    }

    /**
     * @param  array<string, string>  $options
     */
    public function addLeadResponse(string $agentUser, array $options): VicidialResponse
    {
        return $this->requestResponse(self::AGENT_URL, $this->agentOptions($agentUser, 'external_add_lead', $options));
    }

    // -------------------------------------------------------------------------
    // change_ingroups
    // -------------------------------------------------------------------------

    /**
     * Change the in-groups of a logged-in agent (`change_ingroups`).
     * The API user needs vicidial_users.change_agent_campaign = 1.
     *
     * @param  array<string, string>  $options
     */
    public function changeInGroups(string $agentUser, array $options): string
    {
        return $this->requestContent(self::AGENT_URL, $this->agentOptions($agentUser, 'change_ingroups', $options));
    }

    /**
     * @param  array<string, string>  $options
     */
    public function changeInGroupsResponse(string $agentUser, array $options): VicidialResponse
    {
        return $this->requestResponse(self::AGENT_URL, $this->agentOptions($agentUser, 'change_ingroups', $options));
    }

    // -------------------------------------------------------------------------
    // update_fields
    // -------------------------------------------------------------------------

    /**
     * Update lead fields shown on the agent screen (`update_fields`).
     *
     * @param  array<string, string>  $fieldsToUpdate  keys from self::UPDATE_FIELDS_ALLOWED
     *
     * @throws InvalidArgumentException for a key the Agent API does not accept
     */
    public function updateFields(string $agentUser, array $fieldsToUpdate): string
    {
        return $this->requestContent(self::AGENT_URL, $this->updateFieldsOptions($agentUser, $fieldsToUpdate));
    }

    /**
     * @param  array<string, string>  $fieldsToUpdate
     *
     * @throws InvalidArgumentException for a key the Agent API does not accept
     */
    public function updateFieldsResponse(string $agentUser, array $fieldsToUpdate): VicidialResponse
    {
        return $this->requestResponse(self::AGENT_URL, $this->updateFieldsOptions($agentUser, $fieldsToUpdate));
    }

    /**
     * @param  array<string, string>  $fieldsToUpdate
     * @return array<string, string>
     */
    private function updateFieldsOptions(string $agentUser, array $fieldsToUpdate): array
    {
        foreach (array_keys($fieldsToUpdate) as $key) {
            if (! in_array($key, self::UPDATE_FIELDS_ALLOWED, true)) {
                throw new InvalidArgumentException(sprintf('%s is not a valid update_fields field', $key));
            }
        }

        return $this->agentOptions($agentUser, 'update_fields', $fieldsToUpdate);
    }

    // -------------------------------------------------------------------------
    // set_timer_action
    // -------------------------------------------------------------------------

    /**
     * Set the timer action for the current call (`set_timer_action`).
     *
     * @param  array<string, string>  $options
     */
    public function setTimerAction(string $agentUser, array $options): string
    {
        return $this->requestContent(self::AGENT_URL, $this->agentOptions($agentUser, 'set_timer_action', $options));
    }

    /**
     * @param  array<string, string>  $options
     */
    public function setTimerActionResponse(string $agentUser, array $options): VicidialResponse
    {
        return $this->requestResponse(self::AGENT_URL, $this->agentOptions($agentUser, 'set_timer_action', $options));
    }

    // -------------------------------------------------------------------------
    // st_login_log / st_get_agent_active_lead
    // -------------------------------------------------------------------------

    /**
     * Look up a user by vicidial_users.custom_three (`st_login_log`).
     *
     * @param  array<string, string>  $options
     */
    public function stLoginLog(array $options): string
    {
        return $this->requestContent(self::AGENT_URL, $options + ['function' => 'st_login_log']);
    }

    /**
     * @param  array<string, string>  $options
     */
    public function stLoginLogResponse(array $options): VicidialResponse
    {
        return $this->requestResponse(self::AGENT_URL, $options + ['function' => 'st_login_log']);
    }

    /**
     * Output the active lead of a user found by custom_three (`st_get_agent_active_lead`).
     *
     * @param  array<string, string>  $options
     */
    public function stGetAgentActiveLead(array $options): string
    {
        return $this->requestContent(self::AGENT_URL, $options + ['function' => 'st_get_agent_active_lead']);
    }

    /**
     * @param  array<string, string>  $options
     */
    public function stGetAgentActiveLeadResponse(array $options): VicidialResponse
    {
        return $this->requestResponse(self::AGENT_URL, $options + ['function' => 'st_get_agent_active_lead']);
    }

    // -------------------------------------------------------------------------
    // ra_call_control
    // -------------------------------------------------------------------------

    /**
     * Control a remote agent call (`ra_call_control`).
     *
     * @param  array<string, string>  $options
     */
    public function raCallControl(string $agent, array $options): string
    {
        return $this->requestContent(self::AGENT_URL, $this->rawAgentOptions($agent, 'ra_call_control', $options));
    }

    /**
     * @param  array<string, string>  $options
     */
    public function raCallControlResponse(string $agent, array $options): VicidialResponse
    {
        return $this->requestResponse(self::AGENT_URL, $this->rawAgentOptions($agent, 'ra_call_control', $options));
    }

    // -------------------------------------------------------------------------
    // send_dtmf
    // -------------------------------------------------------------------------

    /**
     * Send DTMF digits on the agent's call (`send_dtmf`).
     *
     * @param  array<string, string>  $options
     */
    public function sendDtmf(array $options): string
    {
        return $this->requestContent(self::AGENT_URL, $options + ['function' => 'send_dtmf']);
    }

    /**
     * @param  array<string, string>  $options
     */
    public function sendDtmfResponse(array $options): VicidialResponse
    {
        return $this->requestResponse(self::AGENT_URL, $options + ['function' => 'send_dtmf']);
    }

    // -------------------------------------------------------------------------
    // transfer_conference
    // -------------------------------------------------------------------------

    /**
     * Transfer or conference the agent's call (`transfer_conference`).
     *
     * @param  array<string, string>  $options
     */
    public function transferConference(string $agent, array $options): string
    {
        return $this->requestContent(self::AGENT_URL, $this->rawAgentOptions($agent, 'transfer_conference', $options));
    }

    /**
     * @param  array<string, string>  $options
     */
    public function transferConferenceResponse(string $agent, array $options): VicidialResponse
    {
        return $this->requestResponse(self::AGENT_URL, $this->rawAgentOptions($agent, 'transfer_conference', $options));
    }

    // -------------------------------------------------------------------------
    // park_call
    // -------------------------------------------------------------------------

    /**
     * Park or un-park the customer (`park_call`).
     *
     * @param  array<string, string>  $options
     */
    public function parkCall(string $agent, array $options): string
    {
        return $this->requestContent(self::AGENT_URL, $this->rawAgentOptions($agent, 'park_call', $options));
    }

    /**
     * @param  array<string, string>  $options
     */
    public function parkCallResponse(string $agent, array $options): VicidialResponse
    {
        return $this->requestResponse(self::AGENT_URL, $this->rawAgentOptions($agent, 'park_call', $options));
    }

    // -------------------------------------------------------------------------
    // call_agent
    // -------------------------------------------------------------------------

    /**
     * Call the agent's phone (`call_agent`).
     */
    public function callAgent(string $agentUser, string $value): string
    {
        return $this->requestContent(self::AGENT_URL, $this->valueOptions($agentUser, 'call_agent', $value));
    }

    public function callAgentResponse(string $agentUser, string $value): VicidialResponse
    {
        return $this->requestResponse(self::AGENT_URL, $this->valueOptions($agentUser, 'call_agent', $value));
    }

    // -------------------------------------------------------------------------
    // recording
    // -------------------------------------------------------------------------

    /**
     * Start, stop or query recording of the agent's call (`recording`).
     *
     * @param  array<string, string>  $options
     */
    public function recording(string $agent, array $options): string
    {
        return $this->requestContent(self::AGENT_URL, $this->rawAgentOptions($agent, 'recording', $options));
    }

    /**
     * @param  array<string, string>  $options
     */
    public function recordingResponse(string $agent, array $options): VicidialResponse
    {
        return $this->requestResponse(self::AGENT_URL, $this->rawAgentOptions($agent, 'recording', $options));
    }

    // -------------------------------------------------------------------------
    // webphone_url
    // -------------------------------------------------------------------------

    /**
     * Get the webphone URL of the agent (`webphone_url`).
     */
    public function webPhoneUrl(string $agentUser, string $value): string
    {
        return $this->requestContent(self::AGENT_URL, $this->valueOptions($agentUser, 'webphone_url', trim($value)));
    }

    public function webPhoneUrlResponse(string $agentUser, string $value): VicidialResponse
    {
        return $this->requestResponse(self::AGENT_URL, $this->valueOptions($agentUser, 'webphone_url', trim($value)));
    }

    // -------------------------------------------------------------------------
    // audio_playback
    // -------------------------------------------------------------------------

    /**
     * Play an audio file into the agent's call (`audio_playback`).
     *
     * @param  array<string, string>  $options
     */
    public function audioPlayBack(string $agentUser, array $options): string
    {
        return $this->requestContent(self::AGENT_URL, $this->rawAgentOptions($agentUser, 'audio_playback', $options));
    }

    /**
     * @param  array<string, string>  $options
     */
    public function audioPlayBackResponse(string $agentUser, array $options): VicidialResponse
    {
        return $this->requestResponse(self::AGENT_URL, $this->rawAgentOptions($agentUser, 'audio_playback', $options));
    }

    // -------------------------------------------------------------------------
    // switch_lead
    // -------------------------------------------------------------------------

    /**
     * Switch the lead on the agent screen (`switch_lead`). Sends `lead_id`, which is what the API reads.
     */
    public function switchLead(string $agentUser, string $leadId): string
    {
        return $this->requestContent(self::AGENT_URL, $this->switchLeadOptions($agentUser, $leadId));
    }

    public function switchLeadResponse(string $agentUser, string $leadId): VicidialResponse
    {
        return $this->requestResponse(self::AGENT_URL, $this->switchLeadOptions($agentUser, $leadId));
    }

    /** @return array<string, string> */
    private function switchLeadOptions(string $agentUser, string $leadId): array
    {
        return [
            'agent_user' => trim($agentUser),
            'function' => 'switch_lead',
            'lead_id' => trim($leadId),
        ];
    }

    // -------------------------------------------------------------------------
    // vm_message
    // -------------------------------------------------------------------------

    /**
     * Set a custom voicemail message for the VM button (`vm_message`).
     *
     * @param  array<string, string>  $options
     */
    public function vmMessage(string $agentUser, array $options): string
    {
        return $this->requestContent(self::AGENT_URL, $this->rawAgentOptions($agentUser, 'vm_message', $options));
    }

    /**
     * @param  array<string, string>  $options
     */
    public function vmMessageResponse(string $agentUser, array $options): VicidialResponse
    {
        return $this->requestResponse(self::AGENT_URL, $this->rawAgentOptions($agentUser, 'vm_message', $options));
    }

    // -------------------------------------------------------------------------
    // calls_in_queue_count
    // -------------------------------------------------------------------------

    /**
     * Count the calls waiting in queue for the agent (`calls_in_queue_count`).
     */
    public function callsInQueueCount(string $agentUser, string $status): string
    {
        return $this->requestContent(self::AGENT_URL, $this->valueOptions($agentUser, 'calls_in_queue_count', trim($status)));
    }

    public function callsInQueueCountResponse(string $agentUser, string $status): VicidialResponse
    {
        return $this->requestResponse(self::AGENT_URL, $this->valueOptions($agentUser, 'calls_in_queue_count', trim($status)));
    }

    // -------------------------------------------------------------------------
    // force_fronter_leave_3way / force_fronter_audio_stop
    // -------------------------------------------------------------------------

    /**
     * Force the fronter agent to leave a 3-way call (`force_fronter_leave_3way`).
     */
    public function forceFronterLeave3way(string $agentUser, string $status): string
    {
        return $this->requestContent(self::AGENT_URL, $this->valueOptions($agentUser, 'force_fronter_leave_3way', trim($status)));
    }

    public function forceFronterLeave3wayResponse(string $agentUser, string $status): VicidialResponse
    {
        return $this->requestResponse(self::AGENT_URL, $this->valueOptions($agentUser, 'force_fronter_leave_3way', trim($status)));
    }

    /**
     * Stop audio playback on the fronter's call (`force_fronter_audio_stop`).
     */
    public function forceFronterAudioStop(string $agentUser, string $status): string
    {
        return $this->requestContent(self::AGENT_URL, $this->valueOptions($agentUser, 'force_fronter_audio_stop', trim($status)));
    }

    public function forceFronterAudioStopResponse(string $agentUser, string $status): VicidialResponse
    {
        return $this->requestResponse(self::AGENT_URL, $this->valueOptions($agentUser, 'force_fronter_audio_stop', trim($status)));
    }

    // -------------------------------------------------------------------------
    // stereo_recording
    // -------------------------------------------------------------------------

    /**
     * Send BEGIN / END or ask STATUS of agent-controlled stereo recording (`stereo_recording`).
     *
     * @param  array<string, string>  $options  value (BEGIN, END, STATUS), optional stage (filename suffix, max 14 chars)
     */
    public function stereoRecording(string $agentUser, array $options): string
    {
        return $this->requestContent(self::AGENT_URL, $this->agentOptions($agentUser, 'stereo_recording', $options));
    }

    /**
     * @param  array<string, string>  $options
     */
    public function stereoRecordingResponse(string $agentUser, array $options): VicidialResponse
    {
        return $this->requestResponse(self::AGENT_URL, $this->agentOptions($agentUser, 'stereo_recording', $options));
    }

    // -------------------------------------------------------------------------
    // send_notification
    // -------------------------------------------------------------------------

    /**
     * Send a text alert and/or confetti to agent screens (`send_notification`).
     * Requires "Agent API Notifications" to be enabled on the VICIdial system.
     *
     * @param  array<string, string>  $options  recipient_type (USER, USER_GROUP, CAMPAIGN), recipient, notification_text,
     *                                          notification_date, text_size, text_font, text_weight, text_color,
     *                                          show_confetti (Y/N), duration, maxParticleCount, particleSpeed
     */
    public function sendNotification(array $options): string
    {
        return $this->requestContent(self::AGENT_URL, $options + ['function' => 'send_notification']);
    }

    /**
     * @param  array<string, string>  $options
     */
    public function sendNotificationResponse(array $options): VicidialResponse
    {
        return $this->requestResponse(self::AGENT_URL, $options + ['function' => 'send_notification']);
    }

    // -------------------------------------------------------------------------
    // refresh_panel
    // -------------------------------------------------------------------------

    /**
     * Refresh panels on the agent screen (`refresh_panel`).
     *
     * @param  array<string, string>  $options  any of formreload, scriptreload, script2reload, emailreload, chatreload, callbacksreload set to '1'
     */
    public function refreshPanel(string $agentUser, array $options): string
    {
        return $this->requestContent(self::AGENT_URL, $this->agentOptions($agentUser, 'refresh_panel', $options));
    }

    /**
     * @param  array<string, string>  $options
     */
    public function refreshPanelResponse(string $agentUser, array $options): VicidialResponse
    {
        return $this->requestResponse(self::AGENT_URL, $this->agentOptions($agentUser, 'refresh_panel', $options));
    }

    // -------------------------------------------------------------------------
    // option builders
    // -------------------------------------------------------------------------

    /**
     * agent_user (trimmed) + function + value.
     *
     * @return array<string, string>
     */
    private function valueOptions(string $agentUser, string $function, string $value): array
    {
        return [
            'agent_user' => trim($agentUser),
            'function' => $function,
            'value' => $value,
        ];
    }

    /**
     * Caller options, with agent_user (trimmed) and function as defaults.
     *
     * @param  array<string, string>  $options
     * @return array<string, string>
     */
    private function agentOptions(string $agentUser, string $function, array $options): array
    {
        return $options + [
            'agent_user' => trim($agentUser),
            'function' => $function,
        ];
    }

    /**
     * Caller options with agent_user forced (not trimmed, historical behaviour) and function as default.
     *
     * @param  array<string, string>  $options
     * @return array<string, string>
     */
    private function rawAgentOptions(string $agentUser, string $function, array $options): array
    {
        $options['agent_user'] = $agentUser;

        return $options + ['function' => $function];
    }
}
