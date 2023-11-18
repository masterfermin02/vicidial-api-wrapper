<?php

namespace VicidialApi\Exceptions;

class InvalidIpException extends \InvalidArgumentException {

    public function __construct(
        string $message = "Invalid IP Address or hostname not found",
        int $code = 0,
        \Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
