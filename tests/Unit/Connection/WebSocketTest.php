<?php

namespace JsonRpc\Tests\Unit\Connection;

use JsonRpc\Common\Address;
use JsonRpc\Connection\WebSocket;
use JsonRpc\JsonRpc;
use JsonRpc\Tests\Unit\AbstractTestCase;
use WebSocket\Client as WsClient;

class WebSocketTest extends AbstractTestCase
{
    /**
     * @param Address $address
     * @param string  $expectedRequest
     * @param string  $expectedResponse
     * @param bool    $connectionError
     * @param bool    $readError
     * @param bool    $closeExpected
     *
     * @return WsClient|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createWsClientMock(
        $address,
        $expectedRequest,
        $expectedResponse,
        $connectionError = false,
        $readError = false,
        $closeExpected = true
    ) {
        /** @var WsClient|\PHPUnit_Framework_MockObject_MockObject $ws */
        $ws = $this->getMock('\\WebSocket\\Client', array(), array((string)$address));

        $ws
            ->expects($connectionError || !$closeExpected ? $this->never() : $this->once())
            ->method('close')
            ->with();

        $ws
            ->expects($this->any())
            ->method('isConnected')
            ->willReturn(!$connectionError);

        $ws
            ->expects($connectionError ? $this->never() : $this->once())
            ->method('send')
            ->with($this->equalTo($expectedRequest));

        $ws
            ->expects($connectionError ? $this->never() : $this->once())
            ->method('receive')
            ->with()
            ->willReturn($readError ? 'something else' : $expectedResponse);

        $ws
            ->expects($connectionError ? $this->never() : $this->once())
            ->method('getLastOpcode')
            ->with()
            ->willReturn($readError ? 'not text' : 'text');

        return $ws;
    }

    public function testWsConnection()
    {
        $address = new Address(Address::PROTOCOL_WS, 'localhost', 5555);

        $expectedRequest  = 'test request';
        $expectedResponse = 'test response';

        $wsClientMock = $this->createWsClientMock($address, $expectedRequest, $expectedResponse);

        $conn = new WebSocket($address);

        $this->setPrivatePropertyValue($conn, 'ws', $wsClientMock);

        $response = $conn->send($expectedRequest);

        $this->assertEquals($expectedResponse, $response);
    }

    public function testWssConnection()
    {
        $address = new Address(Address::PROTOCOL_WSS, 'localhost', 5555);

        $expectedRequest  = 'test request';
        $expectedResponse = 'test response';

        $wsClientMock = $this->createWsClientMock($address, $expectedRequest, $expectedResponse);

        $conn = new WebSocket($address);

        $this->setPrivatePropertyValue($conn, 'ws', $wsClientMock);

        $response = $conn->send($expectedRequest);

        $this->assertEquals($expectedResponse, $response);
    }

    public function testPersistentWsConnection()
    {
        $address = new Address(Address::PROTOCOL_WS, 'localhost', 5555);

        $expectedRequest  = 'test request';
        $expectedResponse = 'test response';

        $wsClientMock = $this->createWsClientMock($address, $expectedRequest, $expectedResponse, false, false, false);

        $conn = new WebSocket($address, array(JsonRpc::OPTION_PERSISTENT => true));

        $this->setPrivatePropertyValue($conn, 'ws', $wsClientMock);

        $response = $conn->send($expectedRequest);

        $this->assertEquals($expectedResponse, $response);
    }

    public function testWsConnectionFailedToConnect()
    {
        $address = new Address(Address::PROTOCOL_WS, 'localhost', 5555);

        $expectedRequest  = 'test request';
        $expectedResponse = 'test response';

        $wsClientMock = $this->createWsClientMock($address, $expectedRequest, $expectedResponse, true);

        $conn = new WebSocket($address);

        $this->setPrivatePropertyValue($conn, 'ws', $wsClientMock);

        $this->setExpectedException(
            '\\JsonRpc\\Exception\\ConnectionException',
            'Could not connect to "ws://localhost:5555"'
        );

        $conn->send($expectedRequest);
    }

    public function testWsConnectionFailedToRead()
    {
        $address = new Address(Address::PROTOCOL_WS, 'localhost', 5555);

        $expectedRequest  = 'test request';
        $expectedResponse = 'test response';

        $wsClientMock = $this->createWsClientMock($address, $expectedRequest, $expectedResponse, false, true);

        $conn = new WebSocket($address);

        $this->setPrivatePropertyValue($conn, 'ws', $wsClientMock);

        $this->setExpectedException(
            '\\JsonRpc\\Exception\\ConnectionException',
            'Received non-text frame of type "not text" with text: "something else"'
        );

        $conn->send($expectedRequest);
    }
}
