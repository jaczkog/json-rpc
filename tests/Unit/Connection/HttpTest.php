<?php

namespace JsonRpc\Tests\Unit\Connection;

use JsonRpc\Common\Address;
use JsonRpc\Connection\Http;
use JsonRpc\Connection\Wrapper\Curl;
use JsonRpc\Tests\Unit\AbstractTestCase;

class HttpTest extends AbstractTestCase
{
    private $optionsSet = array();

    /**
     * @param Address $address
     * @param string  $expectedResponse
     * @param bool    $connectionError
     *
     * @return Curl|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createWrapperMock(
        $address,
        $expectedResponse,
        $connectionError = false
    ) {
        $resource = uniqid('r');

        /** @var Curl|\PHPUnit_Framework_MockObject_MockObject $socketWrapper */
        $socketWrapper = $this->getMock('\\JsonRpc\\Connection\\Wrapper\\Curl', array());

        $socketWrapper
            ->expects($this->once())
            ->method('init')
            ->with(
                $this->equalTo((string)$address)
            )
            ->willReturn($connectionError ? false : $resource);

        $socketWrapper
            ->expects($connectionError ? $this->never() : $this->once())
            ->method('close')
            ->with(
                $this->equalTo($resource)
            );

        $socketWrapper
            ->expects($this->any())
            ->method('isConnected')
            ->willReturnCallback(
                function ($r) use ($resource) {
                    return $r == $resource;
                }
            );

        $optionsSet = &$this->optionsSet;
        $socketWrapper
            ->expects($connectionError ? $this->never() : $this->atLeast(4))
            ->method('setopt')
            ->with(
                $this->equalTo($resource)
            )
            ->willReturnCallback(
                function ($handle, $option, $value) use (&$optionsSet) {
                    $optionsSet[$option] = $value;

                    return true;
                }
            );

        $socketWrapper
            ->expects($connectionError ? $this->never() : $this->once())
            ->method('exec')
            ->with(
                $this->equalTo($resource)
            )
            ->willReturn($expectedResponse);

        return $socketWrapper;
    }

    public function testHttpConnection()
    {
        $address = new Address(Address::PROTOCOL_HTTP, 'localhost', 80);

        $expectedRequest  = 'test request';
        $expectedResponse = 'test response';

        $wrapperMock = $this->createWrapperMock($address, $expectedResponse);

        $conn = new Http($address);

        $this->setPrivatePropertyValue($conn, 'wrapper', $wrapperMock);

        $response = $conn->send($expectedRequest);

        $this->assertEquals($expectedResponse, $response);

        $this->assertArrayHasKey(CURLOPT_RETURNTRANSFER, $this->optionsSet);
        $this->assertEquals(true, $this->optionsSet[CURLOPT_RETURNTRANSFER]);

        $this->assertArrayHasKey(CURLOPT_POST, $this->optionsSet);
        $this->assertEquals(true, $this->optionsSet[CURLOPT_POST]);

        $this->assertArrayHasKey(CURLOPT_POSTFIELDS, $this->optionsSet);
        $this->assertEquals($expectedRequest, $this->optionsSet[CURLOPT_POSTFIELDS]);

        $this->assertArrayHasKey(CURLOPT_HTTPHEADER, $this->optionsSet);
        $this->assertContains('Content-Type: application/json', $this->optionsSet[CURLOPT_HTTPHEADER]);
    }

    public function testHttpsConnection()
    {
        $address = new Address(Address::PROTOCOL_HTTPS, 'localhost', 443);

        $expectedRequest  = 'test request';
        $expectedResponse = 'test response';

        $socketWrapper = $this->createWrapperMock($address, $expectedResponse);

        $tcp = new Http($address);

        $this->setPrivatePropertyValue($tcp, 'wrapper', $socketWrapper);

        $response = $tcp->send($expectedRequest);

        $this->assertEquals($expectedResponse, $response);

        $this->assertArrayHasKey(CURLOPT_RETURNTRANSFER, $this->optionsSet);
        $this->assertEquals(true, $this->optionsSet[CURLOPT_RETURNTRANSFER]);

        $this->assertArrayHasKey(CURLOPT_POST, $this->optionsSet);
        $this->assertEquals(true, $this->optionsSet[CURLOPT_POST]);

        $this->assertArrayHasKey(CURLOPT_POSTFIELDS, $this->optionsSet);
        $this->assertEquals($expectedRequest, $this->optionsSet[CURLOPT_POSTFIELDS]);

        $this->assertArrayHasKey(CURLOPT_HTTPHEADER, $this->optionsSet);
        $this->assertContains('Content-Type: application/json', $this->optionsSet[CURLOPT_HTTPHEADER]);
    }

    public function testHttpConnectionFailedToConnect()
    {
        $address = new Address(Address::PROTOCOL_HTTP, 'localhost', 5555);

        $expectedRequest  = 'test request';
        $expectedResponse = 'test response';

        $socketWrapper = $this->createWrapperMock($address, $expectedResponse, true);

        $tcp = new Http($address);

        $this->setPrivatePropertyValue($tcp, 'wrapper', $socketWrapper);

        $this->setExpectedException(
            '\\JsonRpc\\Exception\\ConnectionException',
            sprintf('Could not connect to "%s"', $address)
        );

        $tcp->send($expectedRequest);
    }
}
