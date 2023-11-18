<?php

namespace VicidialApi\Resources;

use VicidialApi\Contracts\AgentContract;
use VicidialApi\ValueObjects\Transporter\Payload;
use Exception;

class Agent implements AgentContract
{
    use Concerns\Transportable;

    const AGENT_URL = '/agc/api.php';

    /**
     * Creates the URL for  the external_hangup method and calls 'call_api_url' to execute it
     *
     * @return string
     * @throws Exception
     */
    public function hangup(string $agentUser): string
    {
        $payload = Payload::retrieveWithParameters(self::AGENT_URL, [
            'agent_user' => urlencode(trim($agentUser)),
            'function' => 'external_hangup',
            'value' => '1'
        ]);

        return $this->transporter->requestContent($payload);
    }

    /**
     * Creates the URL for  the external_status method and calls 'call_api_url' to execute it
     * @param array<string, string> $options
     * @return string
     * @throws Exception
     */
    public function dispo(string $agentUser, array $options): string
    {
        $options = $options + [
                'agent_user' => urlencode(trim($agentUser)),
                'function' => 'external_status'
            ];

        $payload = Payload::retrieveWithParameters(self::AGENT_URL, $options);

        return $this->transporter->requestContent($payload);
    }

    /**
     * Creates the URL for  the external_pause method and calls 'call_api_url' to execute it
     * @return string
     * @throws Exception
     */
    public function pause(string $agentUser, string $status): string
    {
        $payload = Payload::retrieveWithParameters(self::AGENT_URL, [
            'agent_user' => urlencode(trim($agentUser)),
            'function' => 'external_pause',
            'value' => urlencode(trim($status))
        ]);

        return $this->transporter->requestContent($payload);
    }


    /**
     * Creates the URL for  the pause_code method and calls 'call_api_url' to execute it
     * @return string
     * @throws Exception
     */
    public function pauseCode(string $agentUser, int $code): string
    {
        $payload = Payload::retrieveWithParameters(self::AGENT_URL, [
            'agent_user' => urlencode(trim($agentUser)),
            'function' => 'pause_code',
            'value' => $code
        ]);

        return $this->transporter->requestContent($payload);
    }

    /**
     * Creates the URL for the webserver method and calls 'call_api_url' to execute it
     * @return string
     * @throws Exception
     */
    public function webserver(): string
    {
        $payload = Payload::retrieveWithParameters(self::AGENT_URL, [
            'function' => 'webserver',
        ]);

        return $this->transporter->requestContent($payload);
    }

    /**
     * Creates the URL for the version method and calls 'call_api_url' to execute it
     * @return string
     * @throws Exception
     */
    public function version(): string
    {
        $payload = Payload::retrieveWithParameters(self::AGENT_URL, [
            'function' => 'version',
        ]);

        return $this->transporter->requestContent($payload);
    }

    /**
     * Creates the URL for the logout method and calls 'call_api_url' to execute it
     * @return string
     * @throws Exception
     */
    public function logout(string $agentUser): string
    {
        $payload = Payload::retrieveWithParameters(self::AGENT_URL, [
            'agent_user' => urlencode(trim($agentUser)),
            'function' => 'logout',
            'value' => 'LOGOUT',
        ]);

        return $this->transporter->requestContent($payload);
    }

    /**
     * Creates the URL for  the external_dial method and calls 'call_api_url' to execute it
     * @param array<string, string> $options
     * @return string
     * @throws Exception
     */
    public function dial(string $agentUser, array $options): string
    {
        if (!isset($options['phone_number']) ) {
            throw new Exception("Please provide a valid phone number");
        }

        $options = $options + [
            'agent_user' => urlencode(trim($agentUser)),
            'function' => 'external_dial',
            'value' => urlencode(trim($options['phone_number'])),
            'phone_code' => urlencode(trim($options['phone_code'] ?? '')),
            'search' => 'YES',
            'preview' => 'NO',
            'focus' => 'YES'
        ];

        $payload = Payload::retrieveWithParameters(self::AGENT_URL, $options);

        return $this->transporter->requestContent($payload);
    }

    /**
     * Sends a SKIP, DIALONLY, ALTDIAL, ADR3DIAL or FINISH when a lead is being previewed or in Manual Alt Dial
     * @return string
     * @throws Exception
     */
    public function previewDial(string $agentUser, string $value): string
    {
        $options = [
            'agent_user' => urlencode(trim($agentUser)),
            'function' => 'preview_dial_action',
            'value' => urlencode(trim($value))
        ];

        $payload = Payload::retrieveWithParameters(self::AGENT_URL, $options);

        return $this->transporter->requestContent($payload);
    }

    /**
     * Adds a lead in the manual dial list of the campaign for logged-in agent. A much simplified add lead function compared to the Non-Agent API function
     * @param array<string, string> $options
     * @return string
     * @throws Exception
     */
    public function addLead(string $agentUser, array $options): string
    {
        $options = $options + [
                'agent_user' => urlencode(trim($agentUser)),
                'function' => 'external_add_lead'
            ];

        $payload = Payload::retrieveWithParameters(self::AGENT_URL, $options);

        return $this->transporter->requestContent($payload);
    }

    /**
     * This function will change the selected in-groups for an agent that is logged into a campaign that allows
     * for inbound calls to be handled. Allows the selected in-groups for an agent to be changed while they are
     * logged-in to the ViciDial Agent screen only. Once changed in this way, the agent would need to log out
     * and back in to be able to select in-groups themselves(If Agent Choose In-Groups is enabled for that user).
     * The blended checkbox can also be changed using this function. The API user performing this function must
     * have vicidial_users.change_agent_campaign = 1.
     *
     * @param array<string, string> $options
     * @return string
     * @throws Exception
     */
    public function changeInGroups(string $agentUser, array $options): string
    {

        $options = $options + [
                'agent_user' => urlencode(trim($agentUser)),
                'function' => 'change_ingroups'
            ];

        $payload = Payload::retrieveWithParameters(self::AGENT_URL, $options);

        return $this->transporter->requestContent($payload);
    }

    /**
     * Creates the URL for  the external_dial method and calls 'call_api_url' to execute it
     *
     * @param array<string, string> $fieldsToUpdate
     * @return string
     * @throws Exception
     */
    public function updateFields(string $agentUser, array $fieldsToUpdate): string
    {
        // According to the API documentation only these fields are allowed to update using this method
        $allowedFields = ["address1","address2","address3","rank","owner","vendor_lead_code",
            "alt_phone","city","comments","country_code","date_of_birth","email","first_name",
            "gender","gmt_offset_now","last_name","middle_initial","phone_number","phone_code",
            "postal_code","province","security_phrase","source_id","state","title"
        ];

        // Validate that every single field to update us valid
        foreach($fieldsToUpdate as $key => $value ) {
            if (!in_array($key, $allowedFields)) {
                throw new Exception("$key is not a valid field");
            }
        }

        $options = $fieldsToUpdate + [
                'agent_user' => urlencode(trim($agentUser)),
                'function' => 'update_fields'
            ];

        $payload = Payload::retrieveWithParameters(self::AGENT_URL, $options);

        return $this->transporter->requestContent($payload);
    }

    /**
     * Updates the fields that are specified with the values. This will update the data
     * that is on the agent's screen in the customer information section.
     *
     * @param array<string, string> $options
     * @return string
     * @throws Exception
     */
    public function setTimerAction(string $agentUser, array $options): string
    {
        $options = $options + [
                'agent_user' => urlencode(trim($agentUser)),
                'function' => 'set_timer_action'
            ];

        $payload = Payload::retrieveWithParameters(self::AGENT_URL, $options);

        return $this->transporter->requestContent($payload);
    }

    /**
     * Looks up the vicidial_users.custom_three field(as "agentId") to associate with a vicidial user ID.
     * If found it will populate the custom_four field with a "teamId" value, then output the vicidial user ID
     *
     * @param array<string, string> $options
     * @return string
     * @throws Exception
     */
    public function stLoginLog(array $options): string
    {
        $options = $options + [
                'function' => 'st_login_log'
            ];

        $payload = Payload::retrieveWithParameters(self::AGENT_URL, $options);

        return $this->transporter->requestContent($payload);
    }

    /**
     * Looks up the vicidial_users.custom_three field(as "agentId") to associate with a vicidial user ID.
     * If found it will output the active lead_id and phone number, vendor_lead_code, province, security_phrase and source_id fields.
     *
     * @param array<string, string> $options
     * @return string
     * @throws Exception
     */
    public function stGetAgentActiveLead(array $options): string
    {
        $options = $options + [
                'function' => 'st_get_agent_active_lead'
            ];

        $payload = Payload::retrieveWithParameters(self::AGENT_URL, $options);

        return $this->transporter->requestContent($payload);
    }

    /**
     *
     * @param array<string, string> $options
     * @return string
     * @throws Exception
     */
    public function raCallControl(string $agent, array $options): string
    {
        $options['agent_user'] = $agent;
        $options = $options + [
                'function' => 'ra_call_control'
            ];

        $payload = Payload::retrieveWithParameters(self::AGENT_URL, $options);

        return $this->transporter->requestContent($payload);
    }

    /**
     *
     * @param array<string, string> $options
     * @return string
     * @throws Exception
     */
    public function sendDtmf(array $options): string
    {
        $options = $options + [
                'function' => 'send_dtmf'
            ];

        $payload = Payload::retrieveWithParameters(self::AGENT_URL, $options);

        return $this->transporter->requestContent($payload);
    }

    /**
     *
     * @param array<string, string> $options
     * @return string
     * @throws Exception
     */
    public function transferConference(string $agent, array $options): string
    {
        $options['agent_user'] = $agent;
        $options = $options + [
                'function' => 'transfer_conference'
            ];

        $payload = Payload::retrieveWithParameters(self::AGENT_URL, $options);

        return $this->transporter->requestContent($payload);
    }

    /**
     *
     * @param array<string, string> $options
     * @return string
     * @throws Exception
     */
    public function parkCall(string $agent, array $options): string
    {
        $options['agent_user'] = $agent;
        $options = $options + [
                'function' => 'park_call'
            ];

        $payload = Payload::retrieveWithParameters(self::AGENT_URL, $options);

        return $this->transporter->requestContent($payload);
    }

    /**
     *
     * @return string
     * @throws Exception
     */
    public function callAgent(string $agentUser, string $value): string
    {
        $options = [
            'function' => 'call_agent',
            'agent_user' => urlencode(trim($agentUser)),
            'value' => $value
        ];

        $payload = Payload::retrieveWithParameters(self::AGENT_URL, $options);

        return $this->transporter->requestContent($payload);
    }

    /**
     *
     * @param array<string, string> $options
     * @return string
     * @throws Exception
     */
    public function recording(string $agent, array $options): string
    {
        $options['agent_user'] = $agent;
        $options = $options + [
                'function' => 'recording'
            ];

        $payload = Payload::retrieveWithParameters(self::AGENT_URL, $options);

        return $this->transporter->requestContent($payload);
    }

    /**
     *
     * @return string
     * @throws Exception
     */
    public function webPhoneUrl(string $agentUser, string $value): string
    {
        $payload = Payload::retrieveWithParameters(self::AGENT_URL, [
            'agent_user' => urlencode(trim($agentUser)),
            'function' => 'webphone_url',
            'value' => urlencode(trim($value))
        ]);

        return $this->transporter->requestContent($payload);
    }

    /**
     *
     * @param array<string, string> $options
     * @return string
     * @throws Exception
     */
    public function audioPlayBack(string $agentUser, array $options): string
    {
        $options['agent_user'] = $agentUser;
        $options = $options + [
                'function' => 'audio_playback'
            ];

        $payload = Payload::retrieveWithParameters(self::AGENT_URL, $options);

        return $this->transporter->requestContent($payload);
    }

    /**
     * Creates the URL for  the pause_code method and calls 'call_api_url' to execute it
     * @return string
     * @throws Exception
     */
    public function switchLead(string $agentUser, string $leadId): string
    {
        $options = [
            'agent_user' => urlencode(trim($agentUser)),
            'function' => 'switch_lead',
            'value' => urlencode(trim($leadId))
        ];

        $payload = Payload::retrieveWithParameters(self::AGENT_URL, $options);

        return $this->transporter->requestContent($payload);
    }

    /**
     * Set a custom voicemail message to be played when agent clicks the VM button on the agent screen
     * @param array<string, string> $options
     * @return string
     * @throws Exception
     */
    public function vmMessage(string $agentUser, array $options): string
    {
        $options['agent_user'] = $agentUser;
        $options = $options + [
                'function' => 'vm_message'
            ];

        $payload = Payload::retrieveWithParameters(self::AGENT_URL, $options);

        return $this->transporter->requestContent($payload);
    }

    /**
     * display a count of the calls waiting in queue for the specific agent
     * @return string
     * @throws Exception
     */
    public function callsInQueueCount(string $agentUser, string $status): string
    {
        $options = [
            'agent_user' => urlencode(trim($agentUser)),
            'function' => 'calls_in_queue_count',
            'value' => urlencode(trim($status))
        ];

        $payload = Payload::retrieveWithParameters(self::AGENT_URL, $options);

        return $this->transporter->requestContent($payload);
    }

    /**
     * @return string
     * @throws Exception
     */
    public function forceFronterLeave3way(string $agentUser, string $status): string
    {
        $options = [
            'agent_user' => urlencode(trim($agentUser)),
            'function' => 'force_fronter_leave_3way',
            'value' => urlencode(trim($status))
        ];

        $payload = Payload::retrieveWithParameters(self::AGENT_URL, $options);

        return $this->transporter->requestContent($payload);
    }

    /**
     *
     * @return string
     * @throws Exception
     */
    public function forceFronterAudioStop(string $agentUser, string $status): string
    {
        $options = [
            'agent_user' => urlencode(trim($agentUser)),
            'function' => 'force_fronter_audio_stop',
            'value' => urlencode(trim($status))
        ];

        $payload = Payload::retrieveWithParameters(self::AGENT_URL, $options);

        return $this->transporter->requestContent($payload);
    }
}
