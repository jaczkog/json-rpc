<?php

namespace JsonRpc\Connection;

use JsonRpc\Common\Address;

abstract class AbstractConnection
{
    /** TCP connection */
    const CONN_TCP = 'tcp';
    /** HTTP connection */
    const CONN_HTTP = 'http';
    /** WebSocket connection */
    const CONN_WS = 'ws';

    /** @var Address */
    protected $address;
    /** @var array */
    protected $options;

    /**
     * @param Address $address
     * @param array   $options
     */
    final public function __construct(Address $address, $options = array())
    {
        $this->address = $address;
        $this->options = $options;

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

    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed|null
     */
    protected function getOption($key, $default = null)
    {
        return array_key_exists($key, $this->options) ? $this->options[$key] : $default;
    }
}
