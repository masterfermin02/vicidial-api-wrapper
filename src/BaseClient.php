<?php

namespace Vicidial\Api\Wrapper;

use Vicidial\Api\Wrapper\Exceptions\InvalidUrlException;
use Exception;
use GrahamCampbell\GuzzleFactory\GuzzleFactory;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;

class BaseClient implements Client
{
    public function __construct(
        public readonly string $api_user,
        public readonly string $api_password,
        public readonly string $source,
        public readonly ?GuzzleClient $client = null
    ) {
    }

    public static function make(
        string $apiUser,
        string $apiPassword,
        string $source = 'test',
        ?GuzzleClient $client = null
    ): self {
        return new static(
            urlencode($apiUser),
            urlencode($apiPassword),
            urlencode($source),
            $client ?? new GuzzleClient(['handler' => GuzzleFactory::handler()])
        );
    }

    /**
     * @param string $url
     * @param array  $options
     *
     * @return string
     * @throws Exception
     */
    public function callApiUrl(string $url, array $options): string
    {
        if (filter_var(urldecode($url), FILTER_VALIDATE_URL) === false) {
            throw new InvalidUrlException("URL may contain malicious code: $url");
        }

        $options += [
            'user'   => $this->api_user,
            'pass'   => $this->api_password,
            'source' => $this->source,
        ];

        try {
            $response = $this->client->get($url . '?' . http_build_query($options));
        } catch (GuzzleException $exception) {
            throw new Exception($exception->getMessage());
        }

        return $response->getBody();
    }

    protected function encode(array $options): array
    {
        return array_map(fn ($option) => urlencode(trim($option)), $options);
    }
}
