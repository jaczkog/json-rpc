<?php

namespace JsonRpc\Exception;

use Exception;

class InvalidVersionException extends Exception
{
    public function __construct($version, $code = 0, Exception $previous = null)
    {
        parent::__construct(sprintf('Invalid version: "%s"', $version), $code, $previous);
    }
}
