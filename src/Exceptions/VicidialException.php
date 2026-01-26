<?php

declare(strict_types=1);

namespace VicidialApi\Exceptions;

use Exception;

/**
 * Base exception for all Vicidial API wrapper errors.
 *
 * Catch this exception to handle any error from the wrapper.
 *
 * @example
 * try {
 *     $response = $client->agent()->updateFieldsResponse("gabriel", $fields);
 * } catch (VicidialException $e) {
 *     // Handle all wrapper errors
 *     echo "Error: " . $e->getMessage();
 * }
 */
class VicidialException extends Exception
{
    /**
     * Creates a new VicidialException instance.
     *
     * @param  string  $message  The exception message
     * @param  int  $code  The exception code
     * @param  \Throwable|null  $previous  Previous exception for chaining
     */
    public function __construct(
        string $message = '',
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
