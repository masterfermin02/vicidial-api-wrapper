<?php

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Tests\TestCase;
use VicidialApi\Client;
use VicidialApi\Factory;

class VicidialAPiTest extends TestCase
{
    public function test_create_client_correctly(): void
    {
        $client = VicidialApi::create(
            'localhost',
            'testUser',
            'testPassword',
        );

        $this->assertInstanceOf(Client::class, $client);
    }

    public function test_create_factory_correctly(): void
    {
        $client = VicidialApi::factory();

        $this->assertInstanceOf(Factory::class, $client);
    }

    public function test_factory_with_custom_url(): void
    {
        $client = VicidialApi::factory()
            ->withBaseUri('http://localhost')
            ->withApiUser('testUser')
            ->withApiPass('testPassword')
            ->make();

        $this->assertInstanceOf(Client::class, $client);
    }

    public function test_factory_with_custom_url_and_custom_client(): void
    {
        $client = VicidialApi::factory()
            ->withBaseUri('http://localhost')
            ->withApiUser('testUser')
            ->withApiPass('testPassword')
            ->withHttpClient(new \GuzzleHttp\Client)
            ->make();

        $this->assertInstanceOf(Client::class, $client);
    }

    public function test_custom_stream_via_factory(): void
    {
        $vicidialApi = VicidialApi::factory()
            ->withBaseUri('http://localhost')
            ->withApiUser('testUser')
            ->withApiPass('testPassword')
            ->withHttpClient($client = new \GuzzleHttp\Client)
            ->withStreamHandler(fn (RequestInterface $request): ResponseInterface => $client->send($request, ['stream' => true]))
            ->make();

        $this->assertInstanceOf(Client::class, $vicidialApi);
    }
}
