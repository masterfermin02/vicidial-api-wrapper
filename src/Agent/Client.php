<?php

namespace Vicidial\Api\Wrapper\Agent;

use Api\Wrapper\BaseClient;
use Vicidial\Api\Wrapper\Exceptions\InvalidIpException;
use Exception;

/**
 * Class Connection
 * @package Api\Wrapper
 */
class Client extends BaseClient {

    /**
     * @var string
     */
    protected $base_url;

    /**
     * Client constructor.
     * @param $server_ip
     * @param $api_user
     * @param $api_password
     * @param $source
     * @param bool $hasSSl
     * @throws InvalidIpException
     */
    public function __construct(
        string $server_ip,
        string $api_user,
        string $api_password,
        string $source = "test",
        bool $hasSSl = true
    ) {
        $this->base_url = $hasSSl ? 'https://' : 'http://';
        $this->base_url .= $this->server_ip . '/agc/api.php';
        parent::__construct($server_ip, $api_user, $api_password, $source);
    }

    /**
     * Creates the URL for  the external_hangup method and calls 'call_api_url' to execute it
     *
     * @param $agent_user
     * @return string
     * @throws Exception
     */
    public function hangup(string $agent_user)
    {
        return $this->call_api_url($this->base_url,[
            'agent_user' => urlencode(trim($agent_user)),
            'function' => 'external_hangup',
            'value' => '1'
        ]);
    }

    /**
     * Creates the URL for  the external_status method and calls 'call_api_url' to execute it
     * @param $agent_user
     * @param $status
     * @return string
     * @throws Exception
     */
    public function dispo(string $agent_user, array $options)
    {
        $options = $this->encode($options) + [
            'agent_user' => urlencode(trim($agent_user)),
            'function' => 'external_status'
        ];
        return $this->call_api_url($this->base_url, $options);
    }

    private function encode(array $options): array
    {
        return array_map(function ($option) {
            return urlencode(trim($option));
        }, $options);
    }

    /**
     * Creates the URL for  the external_pause method and calls 'call_api_url' to execute it
     * @param $agent_user
     * @param $status
     * @return string
     * @throws Exception
     */
    public function pause($agent_user, $status)
    {
        return $this->call_api_url($this->base_url,[
            'agent_user' => urlencode(trim($agent_user)),
            'function' => 'external_pause',
            'value' => urlencode(trim($status))
        ]);
    }


    /**
     * Creates the URL for  the pause_code method and calls 'call_api_url' to execute it
     * @param $agent_user
     * @param $code
     * @return string
     * @throws Exception
     */
    public function pause_code($agent_user, $code)
    {
        return $this->call_api_url($this->base_url,[
            'agent_user' => urlencode(trim($agent_user)),
            'function' => 'pause_code',
            'value' => urlencode(trim($code))
        ]);
    }

    /**
     * Creates the URL for the webserver method and calls 'call_api_url' to execute it
     * @return string
     * @throws Exception
     */
    public function webserver()
    {
        return $this->call_api_url($this->base_url,[
            'function' => 'webserver'
        ]);
    }

    /**
     * Creates the URL for the version method and calls 'call_api_url' to execute it
     * @return string
     * @throws Exception
     */
    public function version()
    {
        return $this->call_api_url($this->base_url,[
            'function' => 'version'
        ]);
    }

    /**
     * Creates the URL for the logout method and calls 'call_api_url' to execute it
     * @param $agent_user
     * @return string
     * @throws Exception
     */
    public function logout($agent_user)
    {
        return $this->call_api_url($this->base_url,[
            'agent_user' => urlencode(trim($agent_user)),
            'function' => 'logout',
            'value' => 'LOGOUT'
        ]);
    }

    /**
     * Creates the URL for  the external_dial method and calls 'call_api_url' to execute it
     * @param $agent_user
     * @param $options
     * @return string
     * @throws Exception
     */
    public function dial($agent_user, $options)
    {
        if (!isset($options['phone_number']) ) {
            throw new Exception("Please provide a valid phone number");
        }

        $options = $this->encode($options) + [
            'agent_user' => urlencode(trim($agent_user)),
            'function' => 'external_dial',
            'value' => urlencode(trim($options['phone_numer'])),
            'phone_code' => urlencode(trim($options['phone_code'] ?? '')),
            'search' => 'YES',
            'preview' => 'NO',
            'focues' => 'YES'
        ];

        return $this->call_api_url($this->base_url, $options);
    }

    /**
     * Sends a SKIP, DIALONLY, ALTDIAL, ADR3DIAL or FINISH when a lead is being previewed or in Manual Alt Dial
     * @param $agent_user
     * @param $value
     * @return string
     * @throws Exception
     */
    public function preview_dial($agent_user, $value)
    {
        $options = [
                'agent_user' => urlencode(trim($agent_user)),
                'function' => 'preview_dial_action',
                'value' => urlencode(trim($value))
        ];

        return $this->call_api_url($this->base_url, $options);
    }

    /**
     * Adds a lead in the manual dial list of the campaign for logged-in agent. A much simplified add lead function compared to the Non-Agent API function
     * @param $agent_user
     * @param $options
     * @return string
     * @throws Exception
     */
    public function add_lead($agent_user, $options)
    {

        $options = $this->encode($options) + [
            'agent_user' => urlencode(trim($agent_user)),
            'function' => 'external_add_lead'
        ];

        return $this->call_api_url($this->base_url, $options);
    }

    /**
     * This function will change the selected in-groups for an agent that is logged into a campaign that allows
     * for inbound calls to be handled. Allows the selected in-groups for an agent to be changed while they are
     * logged-in to the ViciDial Agent screen only. Once changed in this way, the agent would need to log out
     * and back in to be able to select in-groups themselves(If Agent Choose In-Groups is enabled for that user).
     * The blended checkbox can also be changed using this function. The API user performing this function must
     * have vicidial_users.change_agent_campaign = 1.
     *
     * @param $agent_user
     * @param $options
     * @return string
     * @throws Exception
     */
    public function change_ingroups($agent_user, $options)
    {

        $options = $this->encode($options) + [
                'agent_user' => urlencode(trim($agent_user)),
                'function' => 'change_ingroups'
            ];

        return $this->call_api_url($this->base_url, $options);
    }

    /**
     * Creates the URL for  the external_dial method and calls 'call_api_url' to execute it
     * @param $agent_user
     * @param $fields_to_update
     * @return string
     * @throws Exception
     */
    public function update_fields($agent_user, $fields_to_update)
    {
        if ( !is_array($fields_to_update)) {
            throw new Exception('Fields must be an array');
        }

        // According to the API documentation only these fields are allowed to update using this method
        $permited_fields = array("address1","address2","address3","rank","owner","vendor_lead_code",
            "alt_phone","city","comments","country_code","date_of_birth","email","first_name",
            "gender","gmt_offset_now","last_name","middle_initial","phone_number","phone_code",
            "postal_code","province","security_phrase","source_id","state","title"
        );

        // Validate that every single field to update us valid
        foreach( $fields_to_update as $key => $value ){
            if ( !in_array($key, $permited_fields))
                throw new Exception("$key is not a valid field");
        }

        $options = $fields_to_update + [
                'agent_user' => urlencode(trim($agent_user)),
                'function' => 'update_fields'
            ];
        return $this->call_api_url($this->base_url,$options);
    }

    /**
     * Updates the fields that are specified with the values. This will update the data
     * that is on the agent's screen in the customer information section.
     *
     * @param $agent_user
     * @param $options
     * @return string
     * @throws Exception
     */
    public function set_timer_action($agent_user, $options)
    {
        $options = $this->encode($options) + [
                'agent_user' => urlencode(trim($agent_user)),
                'function' => 'set_timer_action'
            ];

        return $this->call_api_url($this->base_url, $options);
    }

    /**
     * Looks up the vicidial_users.custom_three field(as "agentId") to associate with a vicidial user ID.
     * If found it will populate the custom_four field with a "teamId" value, then output the vicidial user ID
     *
     * @param $options
     * @return string
     * @throws Exception
     */
    public function st_login_log($options)
    {
        $options = $this->encode($options) + [
                'function' => 'st_login_log'
            ];

        return $this->call_api_url($this->base_url, $options);
    }

    /**
     * Looks up the vicidial_users.custom_three field(as "agentId") to associate with a vicidial user ID.
     * If found it will output the active lead_id and phone number, vendor_lead_code, province, security_phrase and source_id fields.
     *
     * @param $options
     * @return string
     * @throws Exception
     */
    public function st_get_agent_active_lead($options)
    {
        $options = $this->encode($options) + [
                'function' => 'st_get_agent_active_lead'
            ];

        return $this->call_api_url($this->base_url, $options);
    }

    /**
     *
     * @param $agent
     * @param $options
     * @return string
     * @throws Exception
     */
    public function ra_call_control($agent, $options)
    {
        $options['agent_user'] = $agent;
        $options = $this->encode($options) + [
                'function' => 'ra_call_control'
            ];

        return $this->call_api_url($this->base_url, $options);
    }

    /**
     *
     * @param $options
     * @return string
     * @throws Exception
     */
    public function send_dtmf($options)
    {
        $options = $this->encode($options) + [
                'function' => 'send_dtmf'
            ];

        return $this->call_api_url($this->base_url, $options);
    }

    /**
     *
     * @param $agent
     * @param $options
     * @return string
     * @throws Exception
     */
    public function transfer_conference($agent, $options)
    {
        $options['agent_user'] = $agent;
        $options = $this->encode($options) + [
                'function' => 'transfer_conference'
            ];

        return $this->call_api_url($this->base_url, $options);
    }

    /**
     *
     * @param $agent
     * @param $options
     * @return string
     * @throws Exception
     */
    public function park_call($agent, $options)
    {
        $options['agent_user'] = $agent;
        $options = $this->encode($options) + [
                'function' => 'park_call'
            ];

        return $this->call_api_url($this->base_url, $options);
    }

    /**
     *
     * @param $agent_user
     * @param $value
     * @return string
     * @throws Exception
     */
    public function call_agent($agent_user, $value)
    {
        $options = [
                'function' => 'call_agent',
                'agent_user' => urlencode(trim($agent_user)),
                'value' => $value
            ];

        return $this->call_api_url($this->base_url, $options);
    }

    /**
     *
     * @param $agent
     * @param $options
     * @return string
     * @throws Exception
     */
    public function recording($agent, $options)
    {
        $options['agent_user'] = $agent;
        $options = $this->encode($options) + [
                'function' => 'recording'
            ];

        return $this->call_api_url($this->base_url, $options);
    }

    /**
     *
     * @param $agent_user
     * @param $value
     * @return string
     * @throws Exception
     */
    public function webphone_url($agent_user, $value)
    {
        return $this->call_api_url($this->base_url,[
            'agent_user' => urlencode(trim($agent_user)),
            'function' => 'webphone_url',
            'value' => urlencode(trim($value))
        ]);
    }

    /**
     *
     * @param $agent_user
     * @param $options
     * @return string
     * @throws Exception
     */
    public function audio_playback($agent_user, $options)
    {
        $options['agent_user'] = $agent_user;
        $options = $this->encode($options) + [
                'function' => 'audio_playback'
            ];

        return $this->call_api_url($this->base_url, $options);
    }

    /**
     * Creates the URL for  the pause_code method and calls 'call_api_url' to execute it
     * @param $agent_user
     * @param $lead_id
     * @return string
     * @throws Exception
     */
    public function switch_lead($agent_user, $lead_id)
    {
        return $this->call_api_url($this->base_url,[
            'agent_user' => urlencode(trim($agent_user)),
            'function' => 'switch_lead',
            'value' => urlencode(trim($lead_id))
        ]);
    }

    /**
     * Set a custom voicemail message to be played when agent clicks the VM button on the agent screen
     * @param $agent_user
     * @param $options
     * @return string
     * @throws Exception
     */
    public function vm_message($agent_user, $options)
    {
        $options['agent_user'] = $agent_user;
        $options = $this->encode($options) + [
                'function' => 'vm_message'
            ];

        return $this->call_api_url($this->base_url, $options);
    }

    /**
     * display a count of the calls waiting in queue for the specific agent
     * @param $agent_user
     * @param $status
     * @return string
     * @throws Exception
     */
    public function calls_in_queue_count($agent_user, $status)
    {
        return $this->call_api_url($this->base_url,[
            'agent_user' => urlencode(trim($agent_user)),
            'function' => 'calls_in_queue_count',
            'value' => urlencode(trim($status))
        ]);
    }

    /**
     *
     * @param $agent_user
     * @param $status
     * @return string
     * @throws Exception
     */
    public function force_fronter_leave_3way($agent_user, $status)
    {
        return $this->call_api_url($this->base_url,[
            'agent_user' => urlencode(trim($agent_user)),
            'function' => 'force_fronter_leave_3way',
            'value' => urlencode(trim($status))
        ]);
    }

    /**
     *
     * @param $agent_user
     * @param $status
     * @return string
     * @throws Exception
     */
    public function force_fronter_audio_stop($agent_user, $status)
    {
        return $this->call_api_url($this->base_url,[
            'agent_user' => urlencode(trim($agent_user)),
            'function' => 'force_fronter_leave_3way',
            'value' => urlencode(trim($status))
        ]);
    }

}
