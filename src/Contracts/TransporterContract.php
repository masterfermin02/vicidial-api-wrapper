<?php

declare(strict_types=1);

namespace VicidialApi\Contracts;

use Psr\Http\Message\ResponseInterface;
use VicidialApi\Exceptions\ErrorException;
use VicidialApi\Exceptions\TransporterException;
use VicidialApi\Exceptions\UnserializableResponse;
use VicidialApi\ValueObjects\Transporter\Payload;
use VicidialApi\ValueObjects\Transporter\Response;

interface TransporterContract
{
    /**
     * Sends a request to a server.
     *
     * @return Response<array<array-key, mixed>|string>
     *
     * @throws ErrorException|UnserializableResponse|TransporterException
     */
    public function requestObject(Payload $payload): Response;

    /**
     * Sends a content request to a server.
     *
     * @throws ErrorException|TransporterException
     */
    public function requestContent(Payload $payload): string;

    /**
     * Sends a stream request to a server.
     **
     * @throws ErrorException
     */
    public function requestStream(Payload $payload): ResponseInterface;
}
