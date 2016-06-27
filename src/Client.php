<?php

namespace JsonRpc;

use JsonRpc\Connection\AbstractConnection;
use JsonRpc\Connection\Http;
use JsonRpc\Connection\Tcp;
use JsonRpc\Connection\WebSocket;
use JsonRpc\Exception\ConnectionException;
use JsonRpc\Request\AbstractRequest;
use JsonRpc\Request\BatchRequest;
use JsonRpc\Request\Notification;
use JsonRpc\Request\Request;
use JsonRpc\Response\Response;

class Client extends JsonRpc
{
    /** @var AbstractConnection */
    private $connection;

    /**
     * Client constructor.
     *
     * @param string $address
     * @param string $version
     */
    public function __construct($address, $version)
    {
        parent::__construct($address, $version);

        $this->connection = $this->createConnection();
    }

    /**
     * @param string $method
     * @param array  $params
     *
     * @return Response
     */
    public function sendRequest($method, $params = array())
    {
        return $this->send(new Request($method, $params));
    }

    /**
     * @param array $requests
     *
     * @return Response[]
     */
    public function sendBatchRequest(array $requests)
    {
        return $this->send(new BatchRequest($requests));
    }

    /**
     * @param string $method
     * @param array  $params
     *
     * @return bool
     */
    public function sendNotification($method, $params = array())
    {
        return $this->send(new Notification($method, $params));
    }

    /**
     * @param AbstractRequest $request
     *
     * @return Response|Response[]|bool
     */
    private function send($request)
    {
        $responseString = $this->connection->send($request->toJson($this->version));
        
        if ($request instanceof Notification) {
            return true;
        } else {
            return Response::fromJson($responseString);
        }
    }

    /**
     * @return AbstractConnection
     * @throws ConnectionException
     */
    private function createConnection()
    {
        switch ($this->connectionType) {
            case self::CONN_HTTP:
                return new Http($this->address);
            case self::CONN_WS:
                return new WebSocket($this->address);
            default:
                return new Tcp($this->address);
        }
    }
}
