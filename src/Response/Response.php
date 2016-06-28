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
                sprintf('%s: %s', json_last_error_msg(), $responseString)
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
}
