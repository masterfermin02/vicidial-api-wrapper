<?php

declare(strict_types=1);

namespace VicidialApi\Exceptions;

/**
 * Unexpected response format from Vicidial API.
 *
 * Thrown when the API response cannot be parsed, is missing expected fields,
 * or has an unexpected structure.
 *
 * @example
 * try {
 *     $response = $client->agent()->dialResponse("gabriel", ['phone_number' => '1234567890']);
 * } catch (InvalidResponseException $e) {
 *     echo "Invalid response: " . $e->getMessage();
 *     echo "Raw response: " . $e->getRawResponse();
 * }
 */
class InvalidResponseException extends VicidialException
{
    /**
     * Creates a new InvalidResponseException instance.
     *
     * @param  string  $message  The error message
     * @param  string  $rawResponse  The raw response that failed to parse
     * @param  int  $code  The error code
     * @param  \Throwable|null  $previous  Previous exception for chaining
     */
    public function __construct(
        string $message,
        private readonly string $rawResponse = '',
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Get the raw response that failed to parse.
     *
     * @return string The raw response text
     */
    public function getRawResponse(): string
    {
        return $this->rawResponse;
    }
}
