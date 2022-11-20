<?php

namespace Vicidial\Api\Wrapper\Exceptions;

class InvalidUrlException extends \InvalidArgumentException
{
    public function __construct(
        string $message = "URL may contain malicious code",
        int $code = 0,
        \Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
