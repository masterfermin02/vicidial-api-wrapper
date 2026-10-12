<?php

declare(strict_types=1);

namespace VicidialApi\Laravel\Facades;

use Illuminate\Support\Facades\Facade;
use VicidialApi\VicidialClient;

/**
 * @method static \VicidialApi\Resources\Agent agent()
 * @method static \VicidialApi\Resources\Admin admin()
 * @method static \VicidialApi\Resources\RemoteAgent remoteAgent()
 *
 * @see VicidialClient
 */
final class Vicidial extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return VicidialClient::class;
    }
}
