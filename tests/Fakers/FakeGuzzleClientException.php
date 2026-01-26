<?php

namespace Tests\Fakers;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class FakeGuzzleClientException extends Client
{
    public function get($uri, array $options = []): ResponseInterface
    {
        throw new \Exception('');
    }
}
