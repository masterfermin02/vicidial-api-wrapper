<?php

declare(strict_types=1);

namespace VicidialApi\Resources\Concerns;

use VicidialApi\Responses\VicidialResponse;
use VicidialApi\ValueObjects\Transporter\Payload;

/**
 * Provides response DTO helper methods for resources.
 */
trait HasResponseHelpers
{
    /**
     * Make an API request and return a VicidialResponse DTO.
     *
     * @param  array<string, string>  $parameters
     */
    protected function requestResponse(string $url, array $parameters): VicidialResponse
    {
        $payload = Payload::retrieveWithParameters($url, $parameters);

        return $this->transporter->requestResponse($payload);
    }

    /**
     * Make an API request and return raw string content.
     *
     * @param  array<string, string>  $parameters
     */
    protected function requestContent(string $url, array $parameters): string
    {
        $payload = Payload::retrieveWithParameters($url, $parameters);

        return $this->transporter->requestContent($payload);
    }
}
