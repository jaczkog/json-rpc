<?php

namespace JsonRpc\Connection;

use JsonRpc\Exception\ConnectionException;
use JsonRpc\JsonRpc;
use WebSocket\Client as WsClient;

class WebSocket extends AbstractConnection
{
    /** @var WsClient */
    private $ws;

    protected function init()
    {
        $this->ws = new WsClient((string)$this->address);
    }

    protected function close()
    {
        if ($this->ws->isConnected() &&  !$this->getOption(JsonRpc::OPTION_PERSISTENT, false)) {
            $this->ws->close();
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
        if (!$this->ws->isConnected()) {
            throw new ConnectionException(sprintf('Could not connect to "%s"', $this->address));
        }

        $this->ws->send($payload);

        $response = $this->ws->receive();

        if (($opCode = $this->ws->getLastOpcode()) !== 'text') {
            throw new ConnectionException(
                sprintf('Received non-text frame of type "%s" with text: "%s"', $opCode, $response)
            );
        }

        return $response;
    }
}
