<?php

declare(strict_types=1);

namespace VicidialApi\Diagnostics;

use VicidialApi\Responses\VicidialResponse;

/**
 * Result of one probe made by Ping.
 */
final class EndpointCheck
{
    private function __construct(
        public readonly string $name,
        public readonly string $function,
        public readonly bool $ok,
        public readonly string $summary,
        public readonly ?string $explanation,
        public readonly ?VicidialResponse $response,
    ) {}

    public static function passed(string $name, string $function, string $summary, ?VicidialResponse $response = null): self
    {
        return new self($name, $function, true, $summary, null, $response);
    }

    public static function failed(string $name, string $function, string $summary, string $explanation, ?VicidialResponse $response = null): self
    {
        return new self($name, $function, false, $summary, $explanation, $response);
    }
}
