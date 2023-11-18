<?php

declare(strict_types=1);

namespace VicidialApi\Resources\Concerns;

use VicidialApi\Contracts\TransporterContract;

trait Transportable
{
    /**
     * Creates a Client instance with the given API token.
     */
    public function __construct(private readonly TransporterContract $transporter)
    {
        // ..
    }
}
