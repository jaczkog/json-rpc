<?php

namespace JsonRpc\Connection;

use JsonRpc\Common\Address;

abstract class AbstractConnection
{
    /** @var Address */
    protected $address;

    /**
     * @param Address $address
     */
    final public function __construct(Address $address)
    {
        $this->address = $address;
        $this->init();
    }

    final public function __destruct()
    {
        $this->close();
    }

    protected function init()
    {
    }

    protected function close()
    {
    }

    /**
     * @param string $payload
     *
     * @return string
     */
    abstract public function send($payload);
}
