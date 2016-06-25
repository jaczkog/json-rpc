<?php

namespace JsonRpc\Exception;

use Exception;

class InvalidRequestException extends Exception
{
    public function __construct($request, $code = 0, Exception $previous = null)
    {
        parent::__construct(sprintf('Invalid request: "%s"', $request), $code, $previous);
    }
}
