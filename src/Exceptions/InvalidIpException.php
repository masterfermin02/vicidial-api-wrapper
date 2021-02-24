<?php

namespace Vicidial\Api\Wrapper\Exceptions;

use Exception;
use Throwable;

class InvalidIpException extends Exception {

    public function __construct($message = "Invalid IP Address or hostname not found", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
