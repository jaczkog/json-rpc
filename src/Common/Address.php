<?php

namespace JsonRpc\Common;

class Address
{
    const PROTOCOL_HTTP  = 'http';
    const PROTOCOL_HTTPS = 'https';
    const PROTOCOL_WS    = 'ws';
    const PROTOCOL_WSS   = 'wss';
    const PROTOCOL_TCP   = 'tcp';
    const PROTOCOL_MOCK  = 'mock';

    /** @var string */
    public $protocol;

    /** @var string */
    public $host;

    /** @var int */
    public $port;

    /** @var string */
    public $path;

    /**
     * Address constructor.
     *
     * @param string $protocol
     * @param string $host
     * @param int    $port
     * @param string $path
     */
    public function __construct($protocol, $host, $port = null, $path = null)
    {
        $this->protocol = $protocol;
        $this->host     = $host;
        $this->port     = $port;
        $this->path     = $path;
    }

    public function __toString()
    {
        return $this->protocol . '://' . $this->host . ':' . $this->port . $this->path;
    }
}
