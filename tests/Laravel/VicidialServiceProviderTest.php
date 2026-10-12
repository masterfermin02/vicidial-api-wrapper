<?php

declare(strict_types=1);

namespace Tests\Laravel;

use GuzzleHttp\Psr7\Response;
use Illuminate\Config\Repository;
use Illuminate\Support\Facades\Facade;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Tester\CommandTester;
use Tests\Mocks\MockPsrClient;
use Tests\TestCase;
use VicidialApi\Laravel\Console\PingCommand;
use VicidialApi\Laravel\Facades\Vicidial;
use VicidialApi\Laravel\VicidialServiceProvider;
use VicidialApi\Logger\NullLogger;
use VicidialApi\Resources\Agent;
use VicidialApi\VicidialClient;
use VicidialApi\VicidialConfig;

final class VicidialServiceProviderTest extends TestCase
{
    /**
     * @param  array<string, mixed>  $settings
     */
    private static function container(array $settings): TestApplication
    {
        $container = new TestApplication;
        $container->instance('config', new Repository(['vicidial' => $settings]));

        (new VicidialServiceProvider($container))->register();

        return $container;
    }

    protected function tearDown(): void
    {
        Facade::clearResolvedInstances();
        Facade::setFacadeApplication(null);
    }

    public function test_register_binds_config_and_client_as_singletons(): void
    {
        $container = self::container([
            'host' => 'vicidial.example.com',
            'user' => 'api',
            'pass' => 'secret',
            'protocol' => 'https',
            'timeout' => '45',
            'source' => 'crm',
        ]);

        $config = $container->make(VicidialConfig::class);
        $this->assertSame('vicidial.example.com', $config->getHost());
        $this->assertSame('https://vicidial.example.com', $config->getBaseUri());
        $this->assertSame(45, $config->getTimeout());
        $this->assertSame('crm', $config->getSource());
        $this->assertFalse($config->getRetryConfig()->isEnabled());

        $client = $container->make(VicidialClient::class);
        $this->assertSame($client, $container->make(VicidialClient::class));
        $this->assertSame($client, $container->make('vicidial'));
    }

    public function test_merge_config_from_fills_defaults_from_the_package_config_file(): void
    {
        $container = self::container(['host' => 'h', 'user' => 'u', 'pass' => 'p']);

        /** @var Repository $repository */
        $repository = $container->make('config');
        $this->assertSame('vicidial-api-wrapper', $repository->get('vicidial.source'));
        $this->assertIsArray($repository->get('vicidial.retry'));
        $this->assertFileExists(VicidialServiceProvider::configPath());
    }

    public function test_retry_basic_auth_and_logger_are_wired(): void
    {
        $container = self::container([
            'host' => 'h', 'user' => 'u', 'pass' => 'p',
            'basic_auth' => ['user' => 'web', 'pass' => 'pw'],
            'retry' => ['enabled' => true, 'max_attempts' => 5, 'retry_on_network_errors' => false],
            'log' => true,
        ]);
        $logger = new NullLogger;
        $container->instance(LoggerInterface::class, $logger);

        $config = $container->make(VicidialConfig::class);
        $this->assertTrue($config->hasBasicAuth());
        $this->assertSame('web', $config->getBasicAuthUser());
        $this->assertTrue($config->getRetryConfig()->isEnabled());
        $this->assertSame(5, $config->getRetryConfig()->getMaxAttempts());
        $this->assertFalse($config->getRetryConfig()->shouldRetryOnNetworkErrors());
        $this->assertSame($logger, $config->getLogger());
    }

    public function test_facade_resolves_the_client(): void
    {
        $container = self::container(['host' => 'h', 'user' => 'u', 'pass' => 'p']);
        Facade::setFacadeApplication($container);

        $this->assertInstanceOf(Agent::class, Vicidial::agent());
    }

    public function test_custom_http_client_binding_is_used(): void
    {
        $container = self::container(['host' => 'h', 'user' => 'u', 'pass' => 'p']);
        $mock = new MockPsrClient([new Response(200, ['Content-Type' => 'text/plain'], 'SUCCESS: external_hangup function set - 1|6666')]);
        $container->instance('vicidial.http_client', $mock);

        $response = $container->make(VicidialClient::class)->agent()->hangupResponse('1000');

        $this->assertTrue($response->ok());
        $this->assertCount(1, $mock->getRequests());
        $this->assertStringContainsString('source=vicidial-api-wrapper', (string) $mock->getLastRequest()?->getUri());
    }

    public function test_ping_command_reports_failures_and_exit_code(): void
    {
        $container = self::container(['host' => 'h', 'user' => 'u', 'pass' => 'p']);
        $version = 'VERSION: 2.14-890a|BUILD: 210303-1234|DATE: 2021-03-03 12:00:00|EPOCH: 1614772800';
        $container->instance('vicidial.http_client', new MockPsrClient([
            new Response(200, ['Content-Type' => 'text/plain'], $version),
            new Response(200, ['Content-Type' => 'text/plain'], 'ERROR: Invalid Username/Password: |u|p|'),
            new Response(200, ['Content-Type' => 'text/plain'], $version),
            new Response(200, ['Content-Type' => 'text/plain'], 'ERROR: campaigns_list USER DOES NOT HAVE PERMISSION TO GET CAMPAIGN INFO - u|0'),
        ]));

        $command = new PingCommand;
        $command->setLaravel($container);
        $tester = new CommandTester($command);

        $exit = $tester->execute([]);
        $output = $tester->getDisplay();

        $this->assertSame(1, $exit);
        $this->assertStringContainsString('[OK]   Agent API reachable (version): VICIdial 2.14-890a', $output);
        $this->assertStringContainsString('[FAIL] Agent API credentials (webserver)', $output);
        $this->assertStringContainsString('password', $output);
        $this->assertStringContainsString('user_level', $output);
    }

    public function test_ping_command_succeeds_when_everything_answers(): void
    {
        $container = self::container(['host' => 'h', 'user' => 'u', 'pass' => 'p']);
        $version = 'VERSION: 2.14-890a|BUILD: 1|DATE: 2021-03-03 12:00:00|EPOCH: 1';
        $container->instance('vicidial.http_client', new MockPsrClient([
            new Response(200, ['Content-Type' => 'text/plain'], $version),
            new Response(200, ['Content-Type' => 'text/plain'], "set.timezone: UTC\nhost name: web1"),
            new Response(200, ['Content-Type' => 'text/plain'], $version),
            new Response(200, ['Content-Type' => 'text/plain'], 'TESTCAMP|Test Campaign|Y'),
        ]));

        $command = new PingCommand;
        $command->setLaravel($container);
        $tester = new CommandTester($command);

        $this->assertSame(0, $tester->execute([]));
        $this->assertStringContainsString('authenticated, web server web1', $tester->getDisplay());
    }
}
