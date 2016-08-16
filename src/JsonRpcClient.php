<?php

namespace JsonRpc;

use JsonRpc\Connection\AbstractConnection;
use JsonRpc\Connection\Http;
use JsonRpc\Connection\Tcp;
use JsonRpc\Connection\WebSocket;
use JsonRpc\Request\AbstractRequest;
use JsonRpc\Request\BatchRequest;
use JsonRpc\Request\Notification;
use JsonRpc\Request\Request;
use JsonRpc\Response\Response;

class JsonRpcClient extends JsonRpc
{
    /** @var AbstractConnection */
    private $connection;

    /**
     * Client constructor.
     *
     * @param string $address
     * @param string $version
     * @param array  $options
     */
    public function __construct($address, $version, $options = array())
    {
        parent::__construct($address, $version);

        $this->connection = $this->createConnection($options);
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
     * @param array $options
     *
     * @return AbstractConnection
     */
    private function createConnection($options = array())
    {
        switch ($this->connectionType) {
            case AbstractConnection::CONN_HTTP:
                return new Http($this->address, $options);
            case AbstractConnection::CONN_WS:
                return new WebSocket($this->address, $options);
            default:
                return new Tcp($this->address, $options);
        }
    }
}
