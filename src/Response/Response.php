<?php

namespace JsonRpc\Response;

use JsonRpc\Exception\InvalidResponseException;

class Response
{
    /** @var mixed */
    public $result;
    /** @var Error|null */
    public $error;
    /** @var string */
    public $id;

    /**
     * Response constructor.
     *
     * @param mixed  $result
     * @param Error  $error
     * @param string $id
     */
    public function __construct($result, $error, $id)
    {
        $this->result = $result;
        $this->error  = $error;
        $this->id     = $id;
    }

    /**
     * @return bool
     */
    public function isSuccess()
    {
        return !$this->isError();
    }

    /**
     * @return bool
     */
    public function isError()
    {
        return $this->error instanceof Error;
    }
    
    /**
     * @param string $responseString
     *
     * @return Response|Response[]
     * @throws InvalidResponseException
     */
    public static function fromJson($responseString)
    {
        $responseObj = json_decode($responseString, false);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidResponseException(
                sprintf(
                    '%s: %s',
                    function_exists('json_last_error_msg') ? json_last_error_msg() : self::getLastJsonErrorMessage(),
                    $responseString
                )
            );
        }

        if (is_array($responseObj)) {
            return array_map('self::fromObject', $responseObj);
        } else {
            return self::fromObject($responseObj);
        }
    }

    /**
     * @param object $responseObject
     *
     * @return Response
     * @throws InvalidResponseException
     */
    private static function fromObject($responseObject)
    {
        if (!property_exists($responseObject, 'id') ||
            (!isset($responseObject->result) && !isset($responseObject->error)) ||
            (isset($responseObject->result) && isset($responseObject->error))
        ) {
            throw new InvalidResponseException(var_export($responseObject, true));
        }

        return new Response(
            isset($responseObject->result) ? $responseObject->result : null,
            isset($responseObject->error) ? Error::fromObject($responseObject->error) : null,
            $responseObject->id
        );
    }

    /**
     * Fallback method for json_last_error_msg() using PHP < 5.5.0
     *
     * @return string
     */
    private static function getLastJsonErrorMessage()
    {
        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                return 'No error has occurred';
            case JSON_ERROR_DEPTH:
                return 'The maximum stack depth has been exceeded';
            case JSON_ERROR_STATE_MISMATCH:
                return 'Invalid or malformed JSON';
            case JSON_ERROR_CTRL_CHAR:
                return 'Control character error, possibly incorrectly encoded';
            case JSON_ERROR_SYNTAX:
                return 'Syntax error';
            case JSON_ERROR_UTF8:
                return 'Malformed UTF-8 characters, possibly incorrectly encoded';
            case JSON_ERROR_RECURSION:
                return 'One or more recursive references in the value to be encoded';
            case JSON_ERROR_INF_OR_NAN:
                return 'One or more NAN or INF values in the value to be encoded';
            case JSON_ERROR_UNSUPPORTED_TYPE:
                return 'A value of a type that cannot be encoded was given';
            default:
                return 'Unknown error';
        }
    }
}
