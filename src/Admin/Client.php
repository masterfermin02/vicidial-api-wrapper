<?php

namespace Vicidial\Api\Wrapper\Admin;

use Vicidial\Api\Wrapper\BaseClient;
use Exception;
use Vicidial\Api\Wrapper\Exceptions\InvalidIpException;

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
        $this->base_url .= $server_ip . '/vicidial/non_agent_api.php';
        parent::__construct($api_user, $api_password, $source);
    }

    /**
     * Shows version and build of the API, along with the date/time and timezone
     *
     * @return string
     * @throws Exception
     */
    public function version()
    {
        return $this->call_api_url($this->base_url,[
            'function' => 'version'
        ]);
    }
}
