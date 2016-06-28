<?php

namespace JsonRpc\Connection;

use JsonRpc\Connection\Wrapper\Curl;
use JsonRpc\Exception\ConnectionException;

class Http extends AbstractConnection
{
    /** @var Curl */
    private $wrapper;
    /** @var resource */
    private $handle;

    protected function init()
    {
        $this->wrapper = new Curl();
    }

    protected function close()
    {
        if ($this->wrapper->isConnected($this->handle)) {
            $this->wrapper->close($this->handle);
        }
    }

    /**
     * @param $url
     *
     * @throws ConnectionException
     */
    private function connect($url)
    {
        if (!$this->wrapper->isConnected($this->handle)) {
            $this->handle = $this->wrapper->init($url);

            if (!$this->wrapper->isConnected($this->handle)) {
                throw new ConnectionException(sprintf('Could not connect to "%s"', $url));
            }

            $this->wrapper->setopt($this->handle, CURLOPT_RETURNTRANSFER, true);
            $this->wrapper->setopt($this->handle, CURLOPT_POST, true);
            $this->wrapper->setopt($this->handle, CURLOPT_FOLLOWLOCATION, true);
        }
    }

    /**
     * @param string $payload
     *
     * @return string
     */
    public function send($payload)
    {
        $this->connect((string)$this->address);

        $headers = array(
            'Content-Type: application/json',
        );

        $this->wrapper->setopt($this->handle, CURLOPT_HTTPHEADER, $headers);
        $this->wrapper->setopt($this->handle, CURLOPT_POSTFIELDS, $payload);

        $response = $this->wrapper->exec($this->handle);

        return $response;
    }
}
