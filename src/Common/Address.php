<?php

namespace JsonRpc\Common;

class Address
{
    const PROTO_HTTP  = 'http';
    const PROTO_HTTPS = 'https';
    const PROTO_WS    = 'ws';
    const PROTO_WSS   = 'wss';
    const PROTO_TCP   = 'tcp';

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
