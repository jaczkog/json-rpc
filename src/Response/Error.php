<?php

namespace JsonRpc\Response;

use JsonRpc\Exception\InvalidResponseException;

class Error
{
    /** @var int */
    public $code;
    /** @var string */
    public $message;
    /** @var mixed */
    public $data;

    /**
     * @param int    $code
     * @param string $message
     * @param mixed  $data
     */
    public function __construct($code, $message, $data = null)
    {
        $this->code    = $code;
        $this->message = $message;
        $this->data    = $data;
    }

    /**
     * @param object $error
     *
     * @return Error
     * @throws InvalidResponseException
     */
    public static function fromObject($error)
    {
        if (!isset($error->code) || !isset($error->message)) {
            throw new InvalidResponseException(sprintf('invalid error object: "%s"', var_export($error, true)));
        }

        return new Error(
            $error->code,
            $error->message,
            isset($error->data) ? $error->data : null
        );
    }
}
