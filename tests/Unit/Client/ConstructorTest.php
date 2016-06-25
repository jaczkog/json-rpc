<?php

namespace JsonRpc\Tests\Unit\Client;

use JsonRpc\Client;
use JsonRpc\Common\Address;
use JsonRpc\Tests\Unit\AbstractTestCase;

class ConstructorTest extends AbstractTestCase
{
    public function providerConstructor()
    {
        return array(
            'error - empty address 1'  => array(
                'address'                => null,
                'version'                => '1.0',
                'expectedException'      => '\\JsonRpc\\Exception\\InvalidAddressException',
                'expectedExceptionMsg'   => 'Invalid address: ""',
                'expectedConnectionType' => null,
                'expectedProtocol'       => '',
                'expectedHost'           => '',
                'expectedPort'           => '',
                'expectedPath'           => '',
                'expectedVersion'        => '',
            ),
            'error - empty address 2'  => array(
                'address'                => '',
                'version'                => '1.0',
                'expectedException'      => '\\JsonRpc\\Exception\\InvalidAddressException',
                'expectedExceptionMsg'   => 'Invalid address: ""',
                'expectedConnectionType' => null,
                'expectedProtocol'       => null,
                'expectedHost'           => null,
                'expectedPort'           => null,
                'expectedPath'           => null,
                'expectedVersion'        => null,
            ),
            'error - invalid protocol' => array(
                'address'                => 'ftp://valid_host:11/abc',
                'version'                => '1.0',
                'expectedException'      => '\\JsonRpc\\Exception\\InvalidAddressException',
                'expectedExceptionMsg'   => 'Invalid address: "ftp://valid_host:11/abc"',
                'expectedConnectionType' => null,
                'expectedProtocol'       => null,
                'expectedHost'           => null,
                'expectedPort'           => null,
                'expectedPath'           => null,
                'expectedVersion'        => null,
            ),
            'error - invalid host'     => array(
                'address'                => 'http://invalid host:11/abc',
                'version'                => '1.0',
                'expectedException'      => '\\JsonRpc\\Exception\\InvalidAddressException',
                'expectedExceptionMsg'   => 'Invalid address: "http://invalid host:11/abc"',
                'expectedConnectionType' => null,
                'expectedProtocol'       => null,
                'expectedHost'           => null,
                'expectedPort'           => null,
                'expectedPath'           => null,
                'expectedVersion'        => null,
            ),
            'error - invalid port'     => array(
                'address'                => 'http://valid_host:p1111/abc',
                'version'                => '1.0',
                'expectedException'      => '\\JsonRpc\\Exception\\InvalidAddressException',
                'expectedExceptionMsg'   => 'Invalid address: "http://valid_host:p1111/abc"',
                'expectedConnectionType' => null,
                'expectedProtocol'       => null,
                'expectedHost'           => null,
                'expectedPort'           => null,
                'expectedPath'           => null,
                'expectedVersion'        => null,
            ),
            'error - empty version'    => array(
                'address'                => 'http://valid_host:1111/abc',
                'version'                => null,
                'expectedException'      => '\\JsonRpc\\Exception\\InvalidVersionException',
                'expectedExceptionMsg'   => 'Invalid version: ""',
                'expectedConnectionType' => null,
                'expectedProtocol'       => null,
                'expectedHost'           => null,
                'expectedPort'           => null,
                'expectedPath'           => null,
                'expectedVersion'        => null,
            ),
            'error - invalid version'  => array(
                'address'                => 'http://valid_host:1111/abc',
                'version'                => '1.99',
                'expectedException'      => '\\JsonRpc\\Exception\\InvalidVersionException',
                'expectedExceptionMsg'   => 'Invalid version: "1.99"',
                'expectedConnectionType' => null,
                'expectedProtocol'       => null,
                'expectedHost'           => null,
                'expectedPort'           => null,
                'expectedPath'           => null,
                'expectedVersion'        => null,
            ),

            'tcp'                      => array(
                'address'                => 'tcp://valid_host:1111/',
                'version'                => '1.0',
                'expectedException'      => null,
                'expectedExceptionMsg'   => null,
                'expectedConnectionType' => Client::CONN_TCP,
                'expectedProtocol'       => Address::PROTO_TCP,
                'expectedHost'           => 'valid_host',
                'expectedPort'           => 1111,
                'expectedPath'           => null,
                'expectedVersion'        => Client::VER_1,
            ),
            'tcp without protocol'     => array(
                'address'                => 'valid_host:1111',
                'version'                => '2.0',
                'expectedException'      => null,
                'expectedExceptionMsg'   => null,
                'expectedConnectionType' => Client::CONN_TCP,
                'expectedProtocol'       => Address::PROTO_TCP,
                'expectedHost'           => 'valid_host',
                'expectedPort'           => 1111,
                'expectedPath'           => null,
                'expectedVersion'        => Client::VER_2,
            ),
            'error - tcp missing port' => array(
                'address'                => 'tcp://valid_host',
                'version'                => '1.0',
                'expectedException'      => '\\JsonRpc\\Exception\\InvalidAddressException',
                'expectedExceptionMsg'   => 'Invalid address: "tcp://valid_host"',
                'expectedConnectionType' => null,
                'expectedProtocol'       => null,
                'expectedHost'           => null,
                'expectedPort'           => null,
                'expectedPath'           => null,
                'expectedVersion'        => null,
            ),
            'error - tcp with path'    => array(
                'address'                => 'tcp://valid_host:1111/path',
                'version'                => '1.0',
                'expectedException'      => '\\JsonRpc\\Exception\\InvalidAddressException',
                'expectedExceptionMsg'   => 'Invalid address: "tcp://valid_host:1111/path"',
                'expectedConnectionType' => null,
                'expectedProtocol'       => null,
                'expectedHost'           => null,
                'expectedPort'           => null,
                'expectedPath'           => null,
                'expectedVersion'        => null,
            ),

            'http'                       => array(
                'address'                => 'http://valid_host:8080/path',
                'version'                => '1.0',
                'expectedException'      => null,
                'expectedExceptionMsg'   => null,
                'expectedConnectionType' => Client::CONN_HTTP,
                'expectedProtocol'       => Address::PROTO_HTTP,
                'expectedHost'           => 'valid_host',
                'expectedPort'           => 8080,
                'expectedPath'           => '/path',
                'expectedVersion'        => Client::VER_1,
            ),
            'http without port and path' => array(
                'address'                => 'http://valid_host',
                'version'                => '1.0',
                'expectedException'      => null,
                'expectedExceptionMsg'   => null,
                'expectedConnectionType' => Client::CONN_HTTP,
                'expectedProtocol'       => Address::PROTO_HTTP,
                'expectedHost'           => 'valid_host',
                'expectedPort'           => 80,
                'expectedPath'           => '/',
                'expectedVersion'        => Client::VER_1,
            ),

            'https'                       => array(
                'address'                => 'https://valid_host:8080/path',
                'version'                => '1.0',
                'expectedException'      => null,
                'expectedExceptionMsg'   => null,
                'expectedConnectionType' => Client::CONN_HTTP,
                'expectedProtocol'       => Address::PROTO_HTTPS,
                'expectedHost'           => 'valid_host',
                'expectedPort'           => 8080,
                'expectedPath'           => '/path',
                'expectedVersion'        => Client::VER_1,
            ),
            'https without port and path' => array(
                'address'                => 'https://valid_host',
                'version'                => '1.0',
                'expectedException'      => null,
                'expectedExceptionMsg'   => null,
                'expectedConnectionType' => Client::CONN_HTTP,
                'expectedProtocol'       => Address::PROTO_HTTPS,
                'expectedHost'           => 'valid_host',
                'expectedPort'           => 443,
                'expectedPath'           => '/',
                'expectedVersion'        => Client::VER_1,
            ),

            'ws'                       => array(
                'address'                => 'ws://valid_host:8080/path',
                'version'                => '1.0',
                'expectedException'      => null,
                'expectedExceptionMsg'   => null,
                'expectedConnectionType' => Client::CONN_WS,
                'expectedProtocol'       => Address::PROTO_WS,
                'expectedHost'           => 'valid_host',
                'expectedPort'           => 8080,
                'expectedPath'           => '/path',
                'expectedVersion'        => Client::VER_1,
            ),
            'ws without port and path' => array(
                'address'                => 'ws://valid_host',
                'version'                => '1.0',
                'expectedException'      => null,
                'expectedExceptionMsg'   => null,
                'expectedConnectionType' => Client::CONN_WS,
                'expectedProtocol'       => Address::PROTO_WS,
                'expectedHost'           => 'valid_host',
                'expectedPort'           => 80,
                'expectedPath'           => '/',
                'expectedVersion'        => Client::VER_1,
            ),

            'wss'                       => array(
                'address'                => 'wss://valid_host:8080/path',
                'version'                => '1.0',
                'expectedException'      => null,
                'expectedExceptionMsg'   => null,
                'expectedConnectionType' => Client::CONN_WS,
                'expectedProtocol'       => Address::PROTO_WSS,
                'expectedHost'           => 'valid_host',
                'expectedPort'           => 8080,
                'expectedPath'           => '/path',
                'expectedVersion'        => Client::VER_1,
            ),
            'wss without port and path' => array(
                'address'                => 'wss://valid_host',
                'version'                => '1.0',
                'expectedException'      => null,
                'expectedExceptionMsg'   => null,
                'expectedConnectionType' => Client::CONN_WS,
                'expectedProtocol'       => Address::PROTO_WSS,
                'expectedHost'           => 'valid_host',
                'expectedPort'           => 443,
                'expectedPath'           => '/',
                'expectedVersion'        => Client::VER_1,
            ),
        );
    }

    /**
     * @param string $address
     * @param string $version
     * @param string $expectedException
     * @param string $expectedExceptionMsg
     * @param string $expectedConnectionType
     * @param string $expectedProtocol
     * @param string $expectedHost
     * @param int    $expectedPort
     * @param string $expectedPath
     * @param string $expectedVersion
     *
     * @dataProvider providerConstructor
     */
    public function testConstructor(
        $address,
        $version,
        $expectedException,
        $expectedExceptionMsg,
        $expectedConnectionType,
        $expectedProtocol,
        $expectedHost,
        $expectedPort,
        $expectedPath,
        $expectedVersion
    ) {
        if (!empty($expectedException)) {
            $this->setExpectedException($expectedException, $expectedExceptionMsg);
        }

        $client = new Client($address, $version);

        /** @var Address $address */
        $address = $this->getPrivatePropertyValue($client, 'address');

        $this->assertEquals($expectedProtocol, $address->protocol);
        $this->assertEquals($expectedHost, $address->host);
        $this->assertEquals($expectedPort, $address->port);
        $this->assertEquals($expectedPath, $address->path);
        $this->assertPrivatePropertyEquals($expectedConnectionType, $client, 'connectionType');
        $this->assertPrivatePropertyEquals($expectedVersion, $client, 'version');
    }
}
