<?php

declare(strict_types=1);

use Tests\TestCase;
use VicidialApi\Client;

final class VicidialApiBasicAuthTest extends TestCase
{
    public function test_create_with_basic_auth_can_be_called_statically(): void
    {
        // The README documents this as a static call; it used to be an instance method
        // and failed with "Non-static method cannot be called statically".
        $client = VicidialApi::createWithBasicAuth(
            'localhost',
            'apiUser',
            'apiPassword',
            'agentUser',
            'agentPassword',
        );

        $this->assertInstanceOf(Client::class, $client);
    }
}
