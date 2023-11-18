<?php

use Test\TestCase;

use VicidialApi\Resources\Agent;
use VicidialApi\Resources\Admin;

class ClientTest extends TestCase
{
    public function testHasAgent(): void
    {
        $client = VicidialApi::create(
            'localhost',
            'testUser',
            'testPassword',
        );

        $this->assertInstanceOf(Agent::class, $client->agent());
    }

    public function testHasAdmin(): void
    {
        $client = VicidialApi::create(
            'localhost',
            'testUser',
            'testPassword',
        );

        $this->assertInstanceOf(Admin::class, $client->admin());
    }
}
