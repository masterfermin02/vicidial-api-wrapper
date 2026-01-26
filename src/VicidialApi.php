<?php

declare(strict_types=1);

use VicidialApi\Client;
use VicidialApi\Factory;
use VicidialApi\VicidialClient;
use VicidialApi\VicidialConfig;

final class VicidialApi
{
    /**
     * Create a VicidialClient using a configuration object (recommended).
     */
    public static function client(VicidialConfig $config): VicidialClient
    {
        return VicidialClient::make($config);
    }

    /**
     * Create a client with standard authentication (legacy method).
     *
     * @deprecated Use VicidialApi::client() with VicidialConfig instead
     */
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

    /**
     * Create a client with basic authentication (legacy method).
     *
     * @deprecated Use VicidialApi::client() with VicidialConfig::createWithBasicAuth() instead
     */
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
        return new Factory;
    }
}
