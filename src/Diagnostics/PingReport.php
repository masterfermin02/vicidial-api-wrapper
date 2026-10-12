<?php

declare(strict_types=1);

namespace VicidialApi\Diagnostics;

/**
 * Outcome of Ping::run(): one EndpointCheck per probe.
 */
final class PingReport
{
    /**
     * @param  list<EndpointCheck>  $checks
     */
    public function __construct(private readonly array $checks) {}

    /**
     * @return list<EndpointCheck>
     */
    public function checks(): array
    {
        return $this->checks;
    }

    public function ok(): bool
    {
        foreach ($this->checks as $check) {
            if (! $check->ok) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return list<EndpointCheck>
     */
    public function failures(): array
    {
        return array_values(array_filter($this->checks, fn (EndpointCheck $check): bool => ! $check->ok));
    }

    /**
     * Plain-text rendering, one line per check plus the explanation of each failure.
     */
    public function toText(): string
    {
        $lines = [];

        foreach ($this->checks as $check) {
            $lines[] = sprintf('[%s] %s (%s): %s', $check->ok ? 'OK' : 'FAIL', $check->name, $check->function, $check->summary);

            if ($check->explanation !== null) {
                $lines[] = '       '.$check->explanation;
            }
        }

        return implode("\n", $lines);
    }
}
