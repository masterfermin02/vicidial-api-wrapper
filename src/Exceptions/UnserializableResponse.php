<?php

declare(strict_types=1);

namespace VicidialApi\Exceptions;

use JsonException;

/**
 * Unserializable response exception.
 *
 * Thrown when a response cannot be unserialized (e.g., invalid JSON).
 *
 * @deprecated Use InvalidResponseException instead. Will be removed in v3.0.
 */
final class UnserializableResponse extends InvalidResponseException
{
    /**
     * Creates a new Exception instance.
     */
    public function __construct(JsonException $exception)
    {
        parent::__construct($exception->getMessage(), '', 0, $exception);
    }
}
