<?php

declare(strict_types=1);

namespace VicidialApi\Contracts;

use Psr\Http\Message\ResponseInterface;
use VicidialApi\Exceptions\VicidialException;
use VicidialApi\Responses\VicidialResponse;
use VicidialApi\ValueObjects\Transporter\Payload;
use VicidialApi\ValueObjects\Transporter\Response;

interface TransporterContract
{
    /**
     * Sends a request to a server.
     *
     * @return Response<array<array-key, mixed>|string>
     *
     * @throws VicidialException
     */
    public function requestObject(Payload $payload): Response;

    /**
     * Sends a content request to a server.
     *
     * @throws VicidialException
     */
    public function requestContent(Payload $payload): string;

    /**
     * Sends a request and returns a VicidialResponse DTO.
     *
     * @throws VicidialException
     */
    public function requestResponse(Payload $payload): VicidialResponse;

    /**
     * Sends a stream request to a server.
     *
     * @throws VicidialException
     */
    public function requestStream(Payload $payload): ResponseInterface;
}
