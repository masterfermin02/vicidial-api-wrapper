<?php

declare(strict_types=1);

namespace VicidialApi\Exceptions;

use Psr\Http\Client\ClientExceptionInterface;

/**
 * Network or HTTP transport errors.
 *
 * Thrown when there are connection timeouts, DNS failures, network errors,
 * or other PSR-18 client exceptions.
 *
 * @example
 * try {
 *     $response = $client->agent()->dialResponse("gabriel", ['phone_number' => '1234567890']);
 * } catch (TransportException $e) {
 *     echo "Network error: " . $e->getMessage();
 *     if ($previous = $e->getPrevious()) {
 *         // Access underlying PSR-18 exception
 *     }
 * }
 */
final class TransportException extends VicidialException
{
    /**
     * Creates a new TransportException instance.
     *
     * @param  ClientExceptionInterface  $exception  The PSR-18 client exception
     */
    public function __construct(ClientExceptionInterface $exception)
    {
        parent::__construct($exception->getMessage(), 0, $exception);
    }
}

/**
 * @deprecated Use TransportException instead. Will be removed in v3.0.
 */
class_alias(TransportException::class, 'VicidialApi\Exceptions\TransporterException');
