<?php

declare(strict_types=1);

namespace Tests\Laravel;

use Illuminate\Container\Container;

/**
 * Just enough of Illuminate\Foundation\Application for a ServiceProvider and an
 * Artisan command to run in a unit test without pulling in laravel/framework.
 */
final class TestApplication extends Container
{
    public function runningInConsole(): bool
    {
        return true;
    }

    public function runningUnitTests(): bool
    {
        return true;
    }

    public function configPath(string $path = ''): string
    {
        return '/app/config'.($path !== '' ? '/'.$path : '');
    }
}
