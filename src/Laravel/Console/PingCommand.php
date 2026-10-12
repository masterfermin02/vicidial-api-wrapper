<?php

declare(strict_types=1);

namespace VicidialApi\Laravel\Console;

use Illuminate\Console\Command;
use VicidialApi\Diagnostics\Ping;
use VicidialApi\VicidialClient;

/**
 * `php artisan vicidial:ping`: calls `version` and an authenticated function on both
 * APIs and prints permission or connectivity problems in plain language.
 */
final class PingCommand extends Command
{
    /** @var string */
    protected $signature = 'vicidial:ping';

    /** @var string */
    protected $description = 'Check connectivity, credentials and permissions of the configured VICIdial API user';

    public function handle(VicidialClient $client): int
    {
        $report = (new Ping($client))->run();

        foreach ($report->checks() as $check) {
            $line = sprintf('%s (%s): %s', $check->name, $check->function, $check->summary);

            if ($check->ok) {
                $this->info('[OK]   '.$line);

                continue;
            }

            $this->error('[FAIL] '.$line);
            $this->line('       '.$check->explanation);
        }

        return $report->ok() ? self::SUCCESS : self::FAILURE;
    }
}
