<?php

namespace JsonRpc\Exception;

use Exception;

class InvalidAddressException extends Exception
{
    public function __construct($address, $code = 0, Exception $previous = null)
    {
        parent::__construct(sprintf('Invalid address: "%s"', $address), $code, $previous);
    }
}
