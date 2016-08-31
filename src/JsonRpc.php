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
        $this->address        = Address::parse($address);
        $this->connectionType = $this->getConnectionType($this->address->protocol);
        $this->version        = $this->verifyVersion($version);
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

            case Address::PROTOCOL_MOCK:
                return AbstractConnection::CONN_MOCK;

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
