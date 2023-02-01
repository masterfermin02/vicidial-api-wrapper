<?php

namespace Vicidial\Api\Wrapper\Admin;

use Exception;
use GrahamCampbell\GuzzleFactory\GuzzleFactory;
use GuzzleHttp\Client as GuzzleClient;
use Vicidial\Api\Wrapper\BaseClient;
use Vicidial\Api\Wrapper\Exceptions\InvalidIpException;
use BadMethodCallException;

/**
 * Class Connection
 * @package Api\Wrapper
 * @method string version()
 * @method string moh_list(array $options)
 * @method string vm_list(array $options)
 * @method string blind_monitor(array $options)
 * @method string agent_ingroup_info(array $options)
 * @method string agent_campaigns(array $options)
 * @method string campaigns_list(array $options)
 * @method string hopper_list(array $options)
 * @method string recording_lookup(array $options)
 * @method string did_log_export(array $options)
 * @method string phone_number_log(array $options)
 * @method string agent_stats_export(array $options)
 * @method string user_group_status(array $options)
 * @method string in_group_status(array $options)
 * @method string agent_status(array $options)
 * @method string callid_info(array $options)
 * @method string lead_field_info(array $options)
 * @method string lead_status_search(array $options)
 * @method string ccc_lead_info(array $options)
 * @method string lead_callback_info(array $options)
 * @method string update_log_entry(array $options)
 * @method string add_lead(array $options)
 * @method string update_lead(array $options)
 * @method string add_user(array $options)
 * @method string update_user(array $options)
 * @method string add_group_alias(array $options)
 * @method string add_dnc_phone(array $options)
 * @method string add_fpg_phone(array $options)
 * @method string add_phone(array $options)
 * @method string update_phone(array $options)
 * @method string add_phone_alias(array $options)
 * @method string update_phone_alias(array $options)
 * @method string server_refresh(array $options)
 * @method string add_list(array $options)
 * @method string update_list(array $options)
 * @method string list_info(array $options)
 * @method string list_custom_fields(array $options)
 * @method string check_phone_number(array $options)
 * @method string logged_in_agents(array $options)
 * @method string call_status_stats(array $options)
 * @method string call_dispo_report(array $options)
 * @method string update_campaign(array $options)
 * @method string add_did(array $options)
 * @method string update_did(array $options)
 * @method string update_cid_group_entry(array $options)
 * @method string copy_user(array $options)
 */
class Client extends BaseClient
{

    protected string $base_url;
    protected bool   $encodeUrl = false;

    protected $actions = [
        'version',
        'string moh_list',
        'vm_list',
        'blind_monitor',
        'agent_ingroup_info',
        'agent_campaigns',
        'campaigns_list',
        'hopper_list',
        'recording_lookup',
        'did_log_export',
        'phone_number_log',
        'agent_stats_export',
        'user_group_status',
        'in_group_status',
        'agent_status',
        'callid_info',
        'lead_field_info',
        'lead_status_search',
        'ccc_lead_info',
        'lead_callback_info',
        'update_log_entry',
        'add_lead',
        'update_lead',
        'add_user',
        'update_user',
        'add_group_alias',
        'add_dnc_phone',
        'add_fpg_phone',
        'add_phone',
        'update_phone',
        'add_phone_alias',
        'update_phone_alias',
        'server_refresh',
        'add_list',
        'update_list',
        'list_info',
        'list_custom_fields',
        'check_phone_number',
        'logged_in_agents',
        'call_status_stats',
        'call_dispo_report',
        'update_campaign',
        'add_did',
        'update_did',
        'update_cid_group_entry',
        'copy_user',
    ];

    /**
     * Client constructor.
     *
     * @param      $server_ip
     * @param      $api_user
     * @param      $api_password
     * @param      $source
     * @param bool $hasSSl
     *
     * @throws InvalidIpException
     */
    public function __construct(
        string $server_ip,
        string $api_user,
        string $api_password,
        string $source = "test",
        bool $hasSSl = true,
        ?GuzzleClient $client = null
    ) {
        $this->base_url = $hasSSl ? 'https://' : 'http://';
        $this->base_url .= $server_ip . '/vicidial/non_agent_api.php';
        parent::__construct(
            $api_user,
            $api_password,
            $source,
            $client ?? new GuzzleClient(['handler' => GuzzleFactory::handler()])
        );
    }

    public static function create(
        string $serverIp,
        string $apiUser,
        string $apiPassword,
        string $source = 'test',
        bool $hasSSl = true,
        ?GuzzleClient $client = null
    ): self {
        return new static(
            $serverIp,
            urlencode($apiUser),
            urlencode($apiPassword),
            urlencode($source),
            $hasSSl,
            $client
        );
    }

    public function withUrlEncode(bool $encodeUrl): self
    {
        $this->encodeUrl = $encodeUrl;

        return $this;
    }

    /**
     * Make the api call and return an string
     *
     * @param string $fun
     * @param array  $options
     *
     * @return string
     * @throws BadMethodCallException
     * @throws Exception
     */
    public function run(string $fun, array $options = []): string
    {
        if (!in_array($fun, $this->actions)) {
            throw new BadMethodCallException("Method {$fun} does not exist");
        }
        $options = $this->encodeUrl ? $this->encode($options) : $options;

        return $this->callApiUrl(
            $this->base_url,
            $options + [
                'function' => $fun,
            ]
        );
    }

    /**
     * Handle calls to non-existent methods.
     *
     * @param string $method
     * @param array  $arguments
     *
     * @return mixed
     */
    public function __call($method, array $arguments = [])
    {
        if (empty($arguments)) {
            return call_user_func([$this, 'run'], $method, $arguments);
        }

        return call_user_func([$this, 'run'], $method, $arguments[0]);
    }
}
