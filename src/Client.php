<?php

namespace VicidialApi;

use VicidialApi\Contracts\TransporterContract;
use VicidialApi\Resources\Agent;
use VicidialApi\Resources\Admin;

final class Client
{
    public function __construct(
        public readonly TransporterContract $transporter,
    ) {
        // ...
    }

    public function agent(): Agent
    {
        return new Agent($this->transporter);
    }

    public function admin(): Admin
    {
        return new Admin($this->transporter);
    }
}
