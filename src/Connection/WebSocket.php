<?php

namespace JsonRpc\Connection;

class WebSocket extends AbstractConnection
{
    /**
     * @param string $payload
     *
     * @return string
     * @throws \Exception
     */
    public function send($payload)
    {
        throw new \Exception('Not yet implemented');
    }
}
