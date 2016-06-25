<?php

namespace JsonRpc;

use JsonRpc\Common\Address;
use JsonRpc\Exception\InvalidAddressException;
use JsonRpc\Exception\InvalidVersionException;

abstract class JsonRpc
{
    /** JSON-RPC version 1.0 */
    const VER_1 = '1.0';
    /** JSON-RPC version 2.0 */
    const VER_2 = '2.0';

    /** TCP connection */
    const CONN_TCP = 'tcp';
    /** HTTP connection */
    const CONN_HTTP = 'http';
    /** WebSocket connection */
    const CONN_WS = 'ws';

    /** @var Address */
    protected $address;
    /** @var string */
    protected $connectionType;
    /** @var string */
    protected $version;

    /**
     * @param string $address
     * @param string $version
     *
     * @throws InvalidAddressException
     * @throws InvalidVersionException
     */
    public function __construct($address, $version = JsonRpc::VER_1)
    {
        $this->address        = $this->parseAddress($address);
        $this->connectionType = $this->getConnectionType($this->address->protocol);
        $this->version        = $this->verifyVersion($version);
    }

    /**
     * @param string $address
     *
     * @return Address
     * @throws InvalidAddressException
     */
    private function parseAddress($address)
    {
        if (!preg_match('~^(?:(https?|wss?|tcp)://)?([a-z0-9\\._-]+)(?::(\d+))?(/.*)?$~', $address, $matches)) {
            throw new InvalidAddressException($address);
        }

        $protocol = !empty($matches[1]) ? strtolower($matches[1]) : Address::PROTO_TCP;
        $host     = $matches[2];
        $port     = !empty($matches[3]) ? (int)$matches[3] : null;
        $path     = !empty($matches[4]) ? $matches[4] : null;

        if ($protocol == Address::PROTO_TCP) {
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
                case Address::PROTO_HTTP:
                case Address::PROTO_WS:
                    $port = 80;
                    break;

                case Address::PROTO_HTTPS:
                case Address::PROTO_WSS:
                    $port = 443;
                    break;
            }
        }

        return new Address($protocol, $host, $port, $path);
    }

    /**
     * @param $protocol
     *
     * @return string
     */
    private function getConnectionType($protocol)
    {
        switch ($protocol) {
            case Address::PROTO_HTTP:
            case Address::PROTO_HTTPS:
                return JsonRpc::CONN_HTTP;

            case Address::PROTO_WS:
            case Address::PROTO_WSS:
                return JsonRpc::CONN_WS;

            default:
                return JsonRpc::CONN_TCP;
        }
    }

    /**
     * @param string $version
     *
     * @return string
     * @throws InvalidVersionException
     */
    private function verifyVersion($version)
    {
        if (!in_array($version, array(JsonRpc::VER_1, JsonRpc::VER_2))) {
            throw new InvalidVersionException($version);
        }

        return $version;
    }
}
