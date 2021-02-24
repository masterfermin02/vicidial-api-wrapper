<?php

namespace Vicidial\Api\Wrapper\Admin;

use Vicidial\Api\Wrapper\Exceptions\InvalidIpException;
use Exception;
use GuzzleHttp\Client as GuzzleClient;
use GrahamCampbell\GuzzleFactory\GuzzleFactory;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Class Connection
 * @package Api\Wrapper
 */
class Client {
    /**
     * @var string
     */
    private $server_ip;
    /**
     * @var string
     */
    private $source;
    /**
     * @var string
     */
    private $api_user;
    /**
     * @var string
     */
    private $api_password;
    /**
     * @var string
     */
    private $base_url;
    /**
     * @var bool
     */
    private $debug;

    /**
     * @var GuzzleClient
     */
    protected $client;

    /**
     * Client constructor.
     * @param $server_ip
     * @param $source
     * @param $api_user
     * @param $api_password
     * @param bool $debug
     * @param bool $hasSSl
     * @throws InvalidIpException
     */
    public function __construct(
        string $server_ip,
        string $source,
        string $api_user,
        string $api_password,
        bool $debug = false,
        bool $hasSSl = true
    ) {
        // Validates if valid IP or resolv hostname WARNING: Not fully tested !!
        if (( filter_var($server_ip, FILTER_VALIDATE_IP ) === false) && ( filter_var(gethostbyname($server_ip), FILTER_VALIDATE_IP) === false ))
        {
            throw new InvalidIpException;
        }

        $this->server_ip = urlencode($server_ip);
        $this->source = urlencode($source);
        $this->api_user = urlencode($api_user);
        $this->api_password = urlencode($api_password);
        $this->debug = $debug;

        $this->base_url = $hasSSl ? 'https://' : 'http://';
        $this->base_url .= $this->server_ip . '/agc/api.php';
        $this->client = new GuzzleClient(['handler' => GuzzleFactory::handler()]);
    }

    /**
     * @param $url
     * @param $options
     * @return string
     * @throws Exception
     */
    public function call_api_url(string $url, array $options)
    {
        if ( filter_var(urldecode($url), FILTER_VALIDATE_URL, FILTER_FLAG_QUERY_REQUIRED) === false )
            throw new Exception("URL may contain malicious code: $url");

        $options += [
            'api_user' => $this->api_user,
            'api_password' => $this->api_password,
            'source' => $this->source
        ];

        try {
            $response = $this->client->get($url, $options);
        } catch (GuzzleException $exception) {
            throw new Exception($exception->getMessage());
        }

        return $response->getBody();
    }
}
