<?php

namespace JsonRpc\Connection;

use JsonRpc\Connection\Wrapper\Socket;
use JsonRpc\Exception\ConnectionException;

class Tcp extends AbstractConnection
{
    /** @var Socket */
    private $wrapper;
    /** @var resource */
    private $handle;

    protected function init()
    {
        $this->wrapper = new Socket();
    }

    protected function close()
    {
        if ($this->wrapper->isConnected($this->handle)) {
            $this->wrapper->close($this->handle);
        }
    }

    /**
     * @throws ConnectionException
     */
    private function connect()
    {
        if (!$this->wrapper->isConnected($this->handle)) {
            $this->handle = $this->wrapper->open($this->address->host, $this->address->port, $errNo, $errStr);

            if (!$this->wrapper->isConnected($this->handle)) {
                throw new ConnectionException(sprintf('%s (%d)', $errStr, $errNo));
            }
        }
    }

    /**
     * @param string $payload
     *
     * @return string
     * @throws ConnectionException
     */
    public function send($payload)
    {
        $this->connect();

        $this->wrapper->write($this->handle, $payload . "\n");
        $this->wrapper->flush($this->handle);

        $response = $this->wrapper->read($this->handle);
        if ($response === false) {
            throw new ConnectionException('could not read');
        }

        return $response;
    }
}
