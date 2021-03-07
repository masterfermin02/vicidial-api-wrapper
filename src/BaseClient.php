<?php


namespace Api\Wrapper;


use Exception;
use GrahamCampbell\GuzzleFactory\GuzzleFactory;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use Vicidial\Api\Wrapper\Exceptions\InvalidIpException;

class BaseClient implements Client
{
    /**
     * @var string
     */
    protected $server_ip;

    /**
     * @var string
     */
    protected $source;

    /**
     * @var string
     */
    protected $api_user;

    /**
     * @var string
     */
    protected $api_password;

    /**
     * @var GuzzleClient
     */
    protected $client;

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
        string $source = "test"
    ) {
        // Validates if valid IP or resolv hostname WARNING: Not fully tested !!
        if (( filter_var($server_ip, FILTER_VALIDATE_IP ) === false) && ( filter_var(gethostbyname($server_ip), FILTER_VALIDATE_IP) === false ))
        {
            throw new InvalidIpException;
        }

        $this->server_ip = urlencode($server_ip);
        $this->source = urlencode($source ?? 'test');
        $this->api_user = urlencode($api_user);
        $this->api_password = urlencode($api_password);
        $this->client = new GuzzleClient(['handler' => GuzzleFactory::handler()]);
    }

    /**
     * @param $url
     * @param $options
     * @return string
     * @throws Exception
     */
    public function call_api_url(string $url, array $options): string
    {
        if ( filter_var(urldecode($url), FILTER_VALIDATE_URL) === false )
            throw new Exception("URL may contain malicious code: $url");

        $options += [
            'user' => $this->api_user,
            'pass' => $this->api_password,
            'source' => $this->source
        ];

        try {
            $response = $this->client->get($url,[
                'form_params' => $options
            ]);
        } catch (GuzzleException $exception) {
            throw new Exception($exception->getMessage());
        }

        return $response->getBody();
    }
}
