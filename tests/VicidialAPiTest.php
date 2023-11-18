<?php

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Test\TestCase;
use VicidialAPi\Client;
use VicidialApi\Factory;

class VicidialAPiTest extends TestCase
{
    public function testCreateClientCorrectly(): void
    {
        $client = VicidialApi::create(
            'localhost',
            'testUser',
            'testPassword',
        );

        $this->assertInstanceOf(Client::class, $client);
    }

    public function testCreateFactoryCorrectly(): void
    {
        $client = VicidialApi::factory();

        $this->assertInstanceOf(Factory::class, $client);
    }

    public function testFactoryWithCustomUrl(): void
    {
        $client = VicidialApi::factory()
            ->withBaseUri('http://localhost')
            ->withApiUser('testUser')
            ->withApiPass('testPassword')
            ->make();

        $this->assertInstanceOf(Client::class, $client);
    }

    public function testFactoryWithCustomUrlAndCustomClient(): void
    {
        $client = VicidialApi::factory()
            ->withBaseUri('http://localhost')
            ->withApiUser('testUser')
            ->withApiPass('testPassword')
            ->withHttpClient(new \GuzzleHttp\Client())
            ->make();

        $this->assertInstanceOf(Client::class, $client);
    }

    public function testCustomStreamViaFactory(): void
    {
        $vicidialApi = VicidialApi::factory()
            ->withBaseUri('http://localhost')
            ->withApiUser('testUser')
            ->withApiPass('testPassword')
            ->withHttpClient($client = new \GuzzleHttp\Client())
            ->withStreamHandler(fn (RequestInterface $request): ResponseInterface => $client->send($request, ['stream' => true]))
            ->make();

        $this->assertInstanceOf(Client::class, $vicidialApi);
    }
}
