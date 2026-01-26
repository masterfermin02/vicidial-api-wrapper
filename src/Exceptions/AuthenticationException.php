<?php

declare(strict_types=1);

namespace VicidialApi\Exceptions;

/**
 * Authentication failed exception.
 *
 * Thrown when API authentication fails due to invalid credentials,
 * missing API user, or invalid authentication configuration.
 *
 * @example
 * try {
 *     $response = $client->agent()->dialResponse("gabriel", ['phone_number' => '1234567890']);
 * } catch (AuthenticationException $e) {
 *     echo "Authentication failed: " . $e->getMessage();
 * }
 */
final class AuthenticationException extends VicidialException {}
