<?php


namespace Vicidial\Api\Wrapper;


interface Client
{
    public function call_api_url(string $url, array $options): string;
}
