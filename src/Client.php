<?php


namespace Vicidial\Api\Wrapper;


interface Client
{
    public function callApiUrl(string $url, array $options): string;
}
