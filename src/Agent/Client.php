<?php

namespace Vicidial\Api\Wrapper\Agent;

use GuzzleHttp\Client as GuzzleClient;
use Vicidial\Api\Wrapper\BaseClient;
use Exception;

/**
 * Class Connection
 * @package Api\Wrapper
 */
class Client extends BaseClient {

    protected string $baseUrl;

    public function __construct(
        string        $serverIp,
        string        $apiUser,
        string        $apiPassword,
        string        $source = "test",
        bool          $hasSSl = true,
        ?GuzzleClient $client = null
    ) {
        $this->baseUrl = $hasSSl ? 'https://' : 'http://';
        $this->baseUrl .= $serverIp . '/agc/api.php';
        parent::__construct($apiUser, $apiPassword, $source, $client);
    }

    public static function create(
        string $serveIp,
        string $apiUser,
        string $apiPassword,
        string $source = 'test',
        bool $hasSSl = true,
        ?GuzzleClient $client = null
    ): self {
        return new static(
            $serveIp,
            urlencode($apiUser),
            urlencode($apiPassword),
            urlencode($source),
            $hasSSl,
            $client
        );
    }

    /**
     * Creates the URL for  the external_hangup method and calls 'call_api_url' to execute it
     *
     * @return string
     * @throws Exception
     */
    public function hangup(string $agentUser): string
    {
        return $this->callApiUrl($this->baseUrl,[
            'agent_user' => urlencode(trim($agentUser)),
            'function' => 'external_hangup',
            'value' => '1'
        ]);
    }

    /**
     * Creates the URL for  the external_status method and calls 'call_api_url' to execute it
     * @return string
     * @throws Exception
     */
    public function dispo(string $agentUser, array $options): string
    {
        $options = $this->encode($options) + [
            'agent_user' => urlencode(trim($agentUser)),
            'function' => 'external_status'
        ];
        return $this->callApiUrl($this->baseUrl, $options);
    }

    /**
     * Creates the URL for  the external_pause method and calls 'call_api_url' to execute it
     * @return string
     * @throws Exception
     */
    public function pause(string $agentUser, string $status): string
    {
        return $this->callApiUrl($this->baseUrl,[
            'agent_user' => urlencode(trim($agentUser)),
            'function' => 'external_pause',
            'value' => urlencode(trim($status))
        ]);
    }


    /**
     * Creates the URL for  the pause_code method and calls 'call_api_url' to execute it
     * @return string
     * @throws Exception
     */
    public function pauseCode(string $agentUser, int $code): string
    {
        return $this->callApiUrl($this->baseUrl,[
            'agent_user' => urlencode(trim($agentUser)),
            'function' => 'pause_code',
            'value' => urlencode(trim($code))
        ]);
    }

    /**
     * Creates the URL for the webserver method and calls 'call_api_url' to execute it
     * @return string
     * @throws Exception
     */
    public function webserver(): string
    {
        return $this->callApiUrl($this->baseUrl,[
            'function' => 'webserver'
        ]);
    }

    /**
     * Creates the URL for the version method and calls 'call_api_url' to execute it
     * @return string
     * @throws Exception
     */
    public function version(): string
    {
        return $this->callApiUrl($this->baseUrl,[
            'function' => 'version'
        ]);
    }

    /**
     * Creates the URL for the logout method and calls 'call_api_url' to execute it
     * @return string
     * @throws Exception
     */
    public function logout(string $agentUser): string
    {
        return $this->callApiUrl($this->baseUrl,[
            'agent_user' => urlencode(trim($agentUser)),
            'function' => 'logout',
            'value' => 'LOGOUT'
        ]);
    }

    /**
     * Creates the URL for  the external_dial method and calls 'call_api_url' to execute it
     * @return string
     * @throws Exception
     */
    public function dial(string $agentUser, array $options): string
    {
        if (!isset($options['phone_number']) ) {
            throw new Exception("Please provide a valid phone number");
        }

        $options = $this->encode($options) + [
            'agent_user' => urlencode(trim($agentUser)),
            'function' => 'external_dial',
            'value' => urlencode(trim($options['phone_number'])),
            'phone_code' => urlencode(trim($options['phone_code'] ?? '')),
            'search' => 'YES',
            'preview' => 'NO',
            'focus' => 'YES'
        ];

        return $this->callApiUrl($this->baseUrl, $options);
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

        return $this->callApiUrl($this->baseUrl, $options);
    }

    /**
     * Adds a lead in the manual dial list of the campaign for logged-in agent. A much simplified add lead function compared to the Non-Agent API function
     * @return string
     * @throws Exception
     */
    public function addLead(string $agentUser, array $options): string
    {
        $options = $this->encode($options) + [
            'agent_user' => urlencode(trim($agentUser)),
            'function' => 'external_add_lead'
        ];

        return $this->callApiUrl($this->baseUrl, $options);
    }

    /**
     * This function will change the selected in-groups for an agent that is logged into a campaign that allows
     * for inbound calls to be handled. Allows the selected in-groups for an agent to be changed while they are
     * logged-in to the ViciDial Agent screen only. Once changed in this way, the agent would need to log out
     * and back in to be able to select in-groups themselves(If Agent Choose In-Groups is enabled for that user).
     * The blended checkbox can also be changed using this function. The API user performing this function must
     * have vicidial_users.change_agent_campaign = 1.
     *
     * @return string
     * @throws Exception
     */
    public function changeInGroups(string $agentUser, array $options): string
    {

        $options = $this->encode($options) + [
                'agent_user' => urlencode(trim($agentUser)),
                'function' => 'change_ingroups'
            ];

        return $this->callApiUrl($this->baseUrl, $options);
    }

    /**
     * Creates the URL for  the external_dial method and calls 'call_api_url' to execute it
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

        return $this->callApiUrl($this->baseUrl,$options);
    }

    /**
     * Updates the fields that are specified with the values. This will update the data
     * that is on the agent's screen in the customer information section.
     *
     * @return string
     * @throws Exception
     */
    public function setTimerAction(string $agentUser, array $options): string
    {
        $options = $this->encode($options) + [
                'agent_user' => urlencode(trim($agentUser)),
                'function' => 'set_timer_action'
            ];

        return $this->callApiUrl($this->baseUrl, $options);
    }

    /**
     * Looks up the vicidial_users.custom_three field(as "agentId") to associate with a vicidial user ID.
     * If found it will populate the custom_four field with a "teamId" value, then output the vicidial user ID
     *
     * @return string
     * @throws Exception
     */
    public function stLoginLog(array $options): string
    {
        $options = $this->encode($options) + [
                'function' => 'st_login_log'
            ];

        return $this->callApiUrl($this->baseUrl, $options);
    }

    /**
     * Looks up the vicidial_users.custom_three field(as "agentId") to associate with a vicidial user ID.
     * If found it will output the active lead_id and phone number, vendor_lead_code, province, security_phrase and source_id fields.
     *
     * @return string
     * @throws Exception
     */
    public function stGetAgentActiveLead(array $options): string
    {
        $options = $this->encode($options) + [
                'function' => 'st_get_agent_active_lead'
            ];

        return $this->callApiUrl($this->baseUrl, $options);
    }

    /**
     *
     * @return string
     * @throws Exception
     */
    public function raCallControl(string $agent, array $options): string
    {
        $options['agent_user'] = $agent;
        $options = $this->encode($options) + [
                'function' => 'ra_call_control'
            ];

        return $this->callApiUrl($this->baseUrl, $options);
    }

    /**
     *
     * @return string
     * @throws Exception
     */
    public function sendDtmf(array $options): string
    {
        $options = $this->encode($options) + [
                'function' => 'send_dtmf'
            ];

        return $this->callApiUrl($this->baseUrl, $options);
    }

    /**
     *
     * @return string
     * @throws Exception
     */
    public function transferConference(string $agent, array $options): string
    {
        $options['agent_user'] = $agent;
        $options = $this->encode($options) + [
                'function' => 'transfer_conference'
            ];

        return $this->callApiUrl($this->baseUrl, $options);
    }

    /**
     *
     * @return string
     * @throws Exception
     */
    public function parkCall(string $agent, array $options): string
    {
        $options['agent_user'] = $agent;
        $options = $this->encode($options) + [
                'function' => 'park_call'
            ];

        return $this->callApiUrl($this->baseUrl, $options);
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

        return $this->callApiUrl($this->baseUrl, $options);
    }

    /**
     *
     * @return string
     * @throws Exception
     */
    public function recording(string $agent, array $options): string
    {
        $options['agent_user'] = $agent;
        $options = $this->encode($options) + [
                'function' => 'recording'
            ];

        return $this->callApiUrl($this->baseUrl, $options);
    }

    /**
     *
     * @return string
     * @throws Exception
     */
    public function webPhoneUrl(string $agentUser, string $value): string
    {
        return $this->callApiUrl($this->baseUrl,[
            'agent_user' => urlencode(trim($agentUser)),
            'function' => 'webphone_url',
            'value' => urlencode(trim($value))
        ]);
    }

    /**
     *
     * @return string
     * @throws Exception
     */
    public function audioPlayBack(string $agentUser, array $options): string
    {
        $options['agent_user'] = $agentUser;
        $options = $this->encode($options) + [
                'function' => 'audio_playback'
            ];

        return $this->callApiUrl($this->baseUrl, $options);
    }

    /**
     * Creates the URL for  the pause_code method and calls 'call_api_url' to execute it
     * @return string
     * @throws Exception
     */
    public function switchLead(string $agentUser, string $leadId): string
    {
        return $this->callApiUrl($this->baseUrl,[
            'agent_user' => urlencode(trim($agentUser)),
            'function' => 'switch_lead',
            'value' => urlencode(trim($leadId))
        ]);
    }

    /**
     * Set a custom voicemail message to be played when agent clicks the VM button on the agent screen
     * @return string
     * @throws Exception
     */
    public function vmMessage(string $agentUser, array $options): string
    {
        $options['agent_user'] = $agentUser;
        $options = $this->encode($options) + [
                'function' => 'vm_message'
            ];

        return $this->callApiUrl($this->baseUrl, $options);
    }

    /**
     * display a count of the calls waiting in queue for the specific agent
     * @return string
     * @throws Exception
     */
    public function callsInQueueCount(string $agentUser, string $status): string
    {
        return $this->callApiUrl($this->baseUrl,[
            'agent_user' => urlencode(trim($agentUser)),
            'function' => 'calls_in_queue_count',
            'value' => urlencode(trim($status))
        ]);
    }

    /**
     *
     * @return string
     * @throws Exception
     */
    public function forceFronterLeave3way(string $agentUser, string $status): string
    {
        return $this->callApiUrl($this->baseUrl,[
            'agent_user' => urlencode(trim($agentUser)),
            'function' => 'force_fronter_leave_3way',
            'value' => urlencode(trim($status))
        ]);
    }

    /**
     *
     * @return string
     * @throws Exception
     */
    public function forceFronterAudioStop(string $agentUser, string $status): string
    {
        return $this->callApiUrl($this->baseUrl,[
            'agent_user' => urlencode(trim($agentUser)),
            'function' => 'force_fronter_leave_3way',
            'value' => urlencode(trim($status))
        ]);
    }
}
