<?php

namespace JsonRpc\Common;

use JsonRpc\Exception\InvalidAddressException;

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

    /**
     * @param string $address
     *
     * @return Address
     * @throws InvalidAddressException
     */
    public static function parse($address)
    {
        if (!preg_match('~^(?:(https?|wss?|tcp|mock)://)?([a-z0-9\\._-]+)(?::(\d+))?(/.*)?$~', $address, $matches)) {
            throw new InvalidAddressException($address);
        }

        $protocol = !empty($matches[1]) ? strtolower($matches[1]) : Address::PROTOCOL_TCP;
        $host     = $matches[2];
        $port     = !empty($matches[3]) ? (int)$matches[3] : null;
        $path     = !empty($matches[4]) ? $matches[4] : null;

        if ($protocol == Address::PROTOCOL_TCP) {
            if (empty($port)) {
                throw new InvalidAddressException($address);
            }
            if (!empty($path)) {
                if ($path != '/') {
                    throw new InvalidAddressException($address);
                } else {
                    $path = null;
                }
            }
        } elseif (empty($path)) {
            $path = '/';
        }

        if (empty($port)) {
            switch ($protocol) {
                case Address::PROTOCOL_HTTP:
                case Address::PROTOCOL_WS:
                    $port = 80;
                    break;

                case Address::PROTOCOL_HTTPS:
                case Address::PROTOCOL_WSS:
                    $port = 443;
                    break;
            }
        }

        return new Address($protocol, $host, $port, $path);
    }

    public function __toString()
    {
        return $this->protocol . '://' . $this->host . ':' . $this->port . $this->path;
    }
}
