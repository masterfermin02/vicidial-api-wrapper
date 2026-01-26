<?php

declare(strict_types=1);

namespace VicidialApi\Logger;

use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;

/**
 * Null logger implementation that does nothing.
 * Used as default when no logger is provided.
 */
final class NullLogger implements LoggerInterface
{
    use LoggerTrait;

    /**
     * {@inheritDoc}
     */
    public function log($level, string|\Stringable $message, array $context = []): void
    {
        // Do nothing
    }
}
