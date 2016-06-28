<?php

namespace JsonRpc\Exception;

use Exception;

class ConnectionException extends Exception
{
    public function __construct($error, $code = 0, Exception $previous = null)
    {
        parent::__construct(sprintf('Connection error: %s', $error), $code, $previous);
    }
}
