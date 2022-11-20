<?php

namespace Vicidial\Api\Tests\Fakers;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

class FakeGuzzleClient extends Client
{
    public function get($uri, array $options = []): ResponseInterface
    {
        return new Response();
    }
}
