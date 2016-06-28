<?php

namespace JsonRpc;

use JsonRpc\Common\Address;
use JsonRpc\Connection\AbstractConnection;
use JsonRpc\Exception\InvalidAddressException;
use JsonRpc\Exception\InvalidVersionException;

abstract class JsonRpc
{
    /** JSON-RPC version 1.0 */
    const VERSION_1 = '1.0';
    /** JSON-RPC version 2.0 */
    const VERSION_2 = '2.0';

    /** Persistent connection */
    const OPTION_PERSISTENT = 'persistent';

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
    public function __construct($address, $version = self::VERSION_1)
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

    /**
     * @param $protocol
     *
     * @return string
     */
    private function getConnectionType($protocol)
    {
        switch ($protocol) {
            case Address::PROTOCOL_HTTP:
            case Address::PROTOCOL_HTTPS:
                return AbstractConnection::CONN_HTTP;

            case Address::PROTOCOL_WS:
            case Address::PROTOCOL_WSS:
                return AbstractConnection::CONN_WS;

            default:
                return AbstractConnection::CONN_TCP;
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
        if (!in_array($version, array(self::VERSION_1, self::VERSION_2))) {
            throw new InvalidVersionException($version);
        }

        return $version;
    }
}
