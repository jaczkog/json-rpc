<?php

namespace JsonRpc\Tests\Unit\Connection;

use JsonRpc\Common\Address;
use JsonRpc\Connection\Tcp;
use JsonRpc\Connection\Wrapper\Socket;
use JsonRpc\JsonRpc;
use JsonRpc\Tests\Unit\AbstractTestCase;

class TcpTest extends AbstractTestCase
{
    /**
     * @param Address $address
     * @param string  $expectedRequest
     * @param string  $expectedResponse
     * @param bool    $connectionError
     * @param bool    $readError
     * @param bool    $closeExpected
     *
     * @return Socket|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createWrapperMock(
        $address,
        $expectedRequest,
        $expectedResponse,
        $connectionError = false,
        $readError = false,
        $closeExpected = true
    ) {
        $resource = uniqid('r');

        /** @var Socket|\PHPUnit_Framework_MockObject_MockObject $wrapper */
        $wrapper = $this->getMock('\\JsonRpc\\Connection\\Wrapper\\Socket', array());

        $wrapper
            ->expects($this->once())
            ->method('open')
            ->with(
                $this->equalTo($address->host),
                $this->equalTo($address->port)
            )
            ->willReturnCallback(
                function ($host, $port, &$errno, &$errstr) use ($resource, $connectionError) {
                    if ($connectionError) {
                        $errno  = -1;
                        $errstr = 'unknown error';

                        return false;
                    }

                    return $resource;
                }
            );

        $wrapper
            ->expects($connectionError || !$closeExpected ? $this->never() : $this->once())
            ->method('close')
            ->with(
                $this->equalTo($resource)
            );

        $wrapper
            ->expects($this->any())
            ->method('isConnected')
            ->willReturnCallback(
                function ($r) use ($resource) {
                    return $r == $resource;
                }
            );

        $wrapper
            ->expects($connectionError ? $this->never() : $this->once())
            ->method('write')
            ->with(
                $this->equalTo($resource),
                $this->equalTo($expectedRequest . "\n")
            )
            ->willReturn(strlen($expectedRequest));

        $wrapper
            ->expects($connectionError ? $this->never() : $this->once())
            ->method('flush')
            ->with(
                $this->equalTo($resource)
            )
            ->willReturn(true);

        $wrapper
            ->expects($connectionError ? $this->never() : $this->once())
            ->method('read')
            ->with(
                $this->equalTo($resource)
            )
            ->willReturn($readError ? false : $expectedResponse);

        return $wrapper;
    }

    public function testTcpConnection()
    {
        $address = new Address(Address::PROTOCOL_TCP, 'localhost', 5555);

        $expectedRequest  = 'test request';
        $expectedResponse = 'test response';

        $wrapperMock = $this->createWrapperMock($address, $expectedRequest, $expectedResponse);

        $conn = new Tcp($address);

        $this->setPrivatePropertyValue($conn, 'wrapper', $wrapperMock);

        $response = $conn->send($expectedRequest);

        $this->assertEquals($expectedResponse, $response);
    }

    public function testPersistentTcpConnection()
    {
        $address = new Address(Address::PROTOCOL_TCP, 'localhost', 5555);

        $expectedRequest  = 'test request';
        $expectedResponse = 'test response';

        $wrapperMock = $this->createWrapperMock($address, $expectedRequest, $expectedResponse, false, false, false);

        $conn = new Tcp($address, array(JsonRpc::OPTION_PERSISTENT => true));

        $this->setPrivatePropertyValue($conn, 'wrapper', $wrapperMock);

        $response = $conn->send($expectedRequest);

        $this->assertEquals($expectedResponse, $response);
    }

    public function testTcpConnectionFailedToConnect()
    {
        $address = new Address(Address::PROTOCOL_TCP, 'localhost', 5555);

        $expectedRequest  = 'test request';
        $expectedResponse = 'test response';

        $wrapperMock = $this->createWrapperMock($address, $expectedRequest, $expectedResponse, true);

        $conn = new Tcp($address);

        $this->setPrivatePropertyValue($conn, 'wrapper', $wrapperMock);

        $this->setExpectedException('\\JsonRpc\\Exception\\ConnectionException', 'unknown error (-1)');

        $conn->send($expectedRequest);
    }

    public function testTcpConnectionFailedToRead()
    {
        $address = new Address(Address::PROTOCOL_TCP, 'localhost', 5555);

        $expectedRequest  = 'test request';
        $expectedResponse = 'test response';

        $wrapperMock = $this->createWrapperMock($address, $expectedRequest, $expectedResponse, false, true);

        $conn = new Tcp($address);

        $this->setPrivatePropertyValue($conn, 'wrapper', $wrapperMock);

        $this->setExpectedException('\\JsonRpc\\Exception\\ConnectionException', 'could not read');

        $conn->send($expectedRequest);
    }
}
