<?php

use Tests\TestCase;
use VicidialApi\Resources\Admin;
use VicidialApi\Resources\Agent;

class ClientTest extends TestCase
{
    public function test_has_agent(): void
    {
        $client = VicidialApi::create(
            'localhost',
            'testUser',
            'testPassword',
        );

        $this->assertInstanceOf(Agent::class, $client->agent());
    }

    public function test_has_admin(): void
    {
        $client = VicidialApi::create(
            'localhost',
            'testUser',
            'testPassword',
        );

        $this->assertInstanceOf(Admin::class, $client->admin());
    }
}
