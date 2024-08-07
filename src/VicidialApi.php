<?php

declare(strict_types=1);

use VicidialApi\Client;
use VicidialApi\Factory;

final class VicidialApi
{
    public static function create(
        string $baseUri,
        string $apiUser,
        string $apiPassword
    ): Client {
        return self::factory()
            ->withBaseUri($baseUri)
            ->withApiUser($apiUser)
            ->withApiPass($apiPassword)
            ->make();
    }

    public function createWithBasicAuth(
        string $baseUri,
        string $apiUser,
        string $apiPassword,
        string $username,
        string $password
    ): Client {
        return self::factory()
            ->withBaseUri($baseUri)
            ->withApiUser($apiUser)
            ->withApiPass($apiPassword)
            ->withBasicAuth($username, $password)
            ->make();
    }

    /**
     * Creates a new factory instance to configure a custom Open AI Client
     */
    public static function factory(): Factory
    {
        return new Factory();
    }
}
