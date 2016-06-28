<?php

namespace JsonRpc\Request;

use JsonRpc\JsonRpc;

abstract class AbstractRequest
{
    /**
     * @param string $version
     *
     * @return string
     */
    abstract public function toJson($version = JsonRpc::VER_1);

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }
}
