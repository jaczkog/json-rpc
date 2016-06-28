<?php

namespace JsonRpc\Request;

use JsonRpc\Exception\InvalidRequestException;
use JsonRpc\JsonRpc;

class Request extends AbstractRequest
{
    /** @var string */
    private $method;
    /** @var array */
    private $params;
    /** @var string */
    private $id;

    /**
     * Request constructor.
     *
     * @param string $method
     * @param array  $params
     * @param string $id
     */
    public function __construct($method, array $params = array(), $id = null)
    {
        $this->method = $method;
        $this->params = $params;
        $this->id     = func_num_args() < 3 ? $this->UUIDv4() : $id;
    }

    /**
     * @param array $array
     *
     * @return Request
     * @throws InvalidRequestException
     */
    public static function fromArray(array $array)
    {
        if (!isset($array[0]) && !isset($array['method'])) {
            throw new InvalidRequestException(var_export($array, true));
        }

        $method = isset($array[0]) ? $array[0] : $array['method'];

        $params = isset($array[1]) ? $array[1] : (isset($array['params']) ? $array['params'] : array());

        if ((isset($array[2]) || isset($array['id']))) {
            $id = isset($array[2]) ? $array[2] : $array['id'];

            return new Request($method, $params, $id);
        } else {
            return new Request($method, $params);
        }
    }

    /**
     * @param string $version
     *
     * @return string
     */
    public function toJson($version = JsonRpc::VERSION_1)
    {
        return json_encode($this->toArray($version));
    }

    /**
     * @param string $version
     *
     * @return array
     */
    public function toArray($version = JsonRpc::VERSION_1)
    {
        $array = array();

        if ($version !== JsonRpc::VERSION_1) {
            $array['jsonrpc'] = $version;
        }

        $array['method'] = $this->method;

        if ($version === JsonRpc::VERSION_1 || !empty($this->params)) {
            $array['params'] = $version === JsonRpc::VERSION_1 ? array_values($this->params) : $this->params;
        }

        if ($version === JsonRpc::VERSION_1 || !empty($this->id)) {
            $array['id'] = $this->id;
        }

        return $array;
    }

    /**
     * @return string
     */
    private function UUIDv4()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            // 16 bits for "time_mid"
            mt_rand(0, 0xffff),
            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand(0, 0x0fff) | 0x4000,
            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand(0, 0x3fff) | 0x8000,
            // 48 bits for "node"
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }
}
