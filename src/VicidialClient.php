<?php

declare(strict_types=1);

namespace VicidialApi;

use GuzzleHttp\Client as GuzzleClient;
use Psr\Http\Client\ClientInterface;
use VicidialApi\Transporters\HttpTransporter;
use VicidialApi\ValueObjects\BasicAuth;
use VicidialApi\ValueObjects\Transporter\BaseUri;
use VicidialApi\ValueObjects\Transporter\Headers;
use VicidialApi\ValueObjects\Transporter\QueryParams;

final class VicidialClient
{
    private Client $client;

    public function __construct(VicidialConfig $config, ?ClientInterface $httpClient = null)
    {
        $this->client = $this->makeClient($config, $httpClient);
    }

    public static function make(VicidialConfig $config, ?ClientInterface $httpClient = null): self
    {
        return new self($config, $httpClient);
    }

    public function agent(): Resources\Agent
    {
        return $this->client->agent();
    }

    public function admin(): Resources\Admin
    {
        return $this->client->admin();
    }

    public function remoteAgent(): Resources\RemoteAgent
    {
        return $this->client->remoteAgent();
    }

    private function makeClient(VicidialConfig $config, ?ClientInterface $httpClient = null): Client
    {
        $headers = Headers::create();

        // Add basic auth if configured
        if ($config->hasBasicAuth()) {
            $basicAuth = new BasicAuth($config->getBasicAuthUser(), $config->getBasicAuthPass());
            $headers = $headers->withBasicAuthorization($basicAuth);
        }

        // Add user agent
        $headers = $headers->withCustomHeader('User-Agent', $config->getUserAgent());

        $baseUri = BaseUri::from($config->getBaseUri());

        // Setup query params
        $queryParams = QueryParams::create()
            ->withParam('source', $config->getSource())
            ->withParam('user', $config->getUser())
            ->withParam('pass', $config->getPass());

        // Create HTTP client with timeout and TLS verification settings
        if (! $httpClient instanceof \Psr\Http\Client\ClientInterface) {
            $httpClient = new GuzzleClient([
                'timeout' => $config->getTimeout(),
                'verify' => $config->isVerifyTls(),
            ]);
        }

        // Create stream handler
        $streamHandler = fn (\Psr\Http\Message\RequestInterface $request): \Psr\Http\Message\ResponseInterface => $httpClient instanceof GuzzleClient
                ? $httpClient->send($request, ['stream' => true])
                : $httpClient->sendRequest($request);

        $transporter = new HttpTransporter($httpClient, $baseUri, $headers, $queryParams, $streamHandler);

        return new Client($transporter);
    }
}
