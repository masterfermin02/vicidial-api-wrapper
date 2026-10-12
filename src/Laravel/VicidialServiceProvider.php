<?php

declare(strict_types=1);

namespace VicidialApi\Laravel;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Psr\Http\Client\ClientInterface;
use Psr\Log\LoggerInterface;
use VicidialApi\Laravel\Console\PingCommand;
use VicidialApi\VicidialClient;
use VicidialApi\VicidialConfig;

/**
 * Laravel integration: binds VicidialConfig and VicidialClient from config/vicidial.php,
 * registers the `vicidial:ping` command and publishes the config file.
 *
 * Auto-discovered through composer.json (extra.laravel). Publish the config with
 * `php artisan vendor:publish --tag=vicidial-config`.
 */
final class VicidialServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(self::configPath(), 'vicidial');

        $this->app->singleton(VicidialConfig::class, function (Container $app): VicidialConfig {
            /** @var Repository $config */
            $config = $app->make('config');

            /** @var array<string, mixed> $settings */
            $settings = $config->get('vicidial', []);

            $logger = ($settings['log'] ?? false) && $app->bound(LoggerInterface::class)
                ? $app->make(LoggerInterface::class)
                : null;

            return VicidialConfig::fromArray($settings, $logger instanceof LoggerInterface ? $logger : null);
        });

        $this->app->singleton(VicidialClient::class, function (Container $app): VicidialClient {
            /** @var VicidialConfig $config */
            $config = $app->make(VicidialConfig::class);

            /** @var ClientInterface|null $httpClient */
            $httpClient = $app->bound('vicidial.http_client') ? $app->make('vicidial.http_client') : null;

            return VicidialClient::make($config, $httpClient);
        });

        $this->app->alias(VicidialClient::class, 'vicidial');
    }

    public function boot(): void
    {
        if (! $this->app instanceof Application || ! $this->app->runningInConsole()) {
            return;
        }

        $this->publishes([self::configPath() => $this->app->configPath('vicidial.php')], 'vicidial-config');
        $this->commands([PingCommand::class]);
    }

    public static function configPath(): string
    {
        return dirname(__DIR__, 2).'/config/vicidial.php';
    }
}
