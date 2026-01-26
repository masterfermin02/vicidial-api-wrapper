<?php

declare(strict_types=1);

namespace VicidialApi\Exceptions;

/**
 * Vicidial API returned an error response.
 *
 * Thrown when the Vicidial API returns an error message,
 * such as "ERROR: invalid user" or other API-level errors.
 *
 * @example
 * try {
 *     $response = $client->agent()->dialResponse("gabriel", ['phone_number' => '1234567890']);
 * } catch (ApiErrorException $e) {
 *     echo "API error: " . $e->getMessage();
 *     echo "Raw response: " . $e->getRawResponse();
 * }
 */
final class ApiErrorException extends VicidialException
{
    /**
     * Creates a new ApiErrorException instance.
     *
     * @param  string  $message  The error message
     * @param  string  $rawResponse  The raw response from Vicidial
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
     * Get the raw response from Vicidial.
     *
     * @return string The raw response text
     */
    public function getRawResponse(): string
    {
        return $this->rawResponse;
    }
}
