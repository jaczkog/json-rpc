<?php

namespace JsonRpc\Exception;

use Exception;

class InvalidResponseException extends Exception
{
    public function __construct($request, $code = 0, Exception $previous = null)
    {
        parent::__construct(sprintf('Invalid response: "%s"', $request), $code, $previous);
    }
}
