<?php

declare(strict_types=1);

namespace VicidialApi\Exceptions;

/**
 * Retry attempts exhausted exception.
 *
 * Thrown when all retry attempts have been exhausted and the request still failed.
 *
 * @example
 * try {
 *     $response = $client->agent()->dialResponse("gabriel", ['phone_number' => '1234567890']);
 * } catch (RetryException $e) {
 *     echo "Failed after " . $e->getAttemptsMade() . " attempts";
 *     echo "Error: " . $e->getMessage();
 * }
 */
final class RetryException extends VicidialException
{
    /**
     * Creates a new RetryException instance.
     *
     * @param  string  $message  The error message
     * @param  int  $attemptsMade  The number of attempts made
     * @param  int  $code  The error code
     * @param  \Throwable|null  $previous  Previous exception for chaining
     */
    public function __construct(
        string $message,
        private readonly int $attemptsMade,
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Get the number of retry attempts that were made.
     *
     * @return int The number of attempts
     */
    public function getAttemptsMade(): int
    {
        return $this->attemptsMade;
    }
}
