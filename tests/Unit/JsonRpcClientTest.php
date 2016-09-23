<?php

namespace JsonRpc\Tests\Unit;

use JsonRpc\Common\Address;
use JsonRpc\Connection\AbstractConnection;
use JsonRpc\Connection\Http;
use JsonRpc\Connection\Tcp;
use JsonRpc\Connection\WebSocket;
use JsonRpc\JsonRpcClient;
use JsonRpc\Request\Notification;
use JsonRpc\Request\Request;
use JsonRpc\Response\Response;

class JsonRpcClientTest extends AbstractTestCase
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
                'expectedConnectionType' => AbstractConnection::CONN_TCP,
                'expectedProtocol'       => Address::PROTOCOL_TCP,
                'expectedHost'           => 'valid_host',
                'expectedPort'           => 1111,
                'expectedPath'           => null,
                'expectedVersion'        => JsonRpcClient::VERSION_1,
            ),
            'tcp without protocol'     => array(
                'address'                => 'valid_host:1111',
                'version'                => '2.0',
                'expectedException'      => null,
                'expectedExceptionMsg'   => null,
                'expectedConnectionType' => AbstractConnection::CONN_TCP,
                'expectedProtocol'       => Address::PROTOCOL_TCP,
                'expectedHost'           => 'valid_host',
                'expectedPort'           => 1111,
                'expectedPath'           => null,
                'expectedVersion'        => JsonRpcClient::VERSION_2,
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
                'expectedConnectionType' => AbstractConnection::CONN_HTTP,
                'expectedProtocol'       => Address::PROTOCOL_HTTP,
                'expectedHost'           => 'valid_host',
                'expectedPort'           => 8080,
                'expectedPath'           => '/path',
                'expectedVersion'        => JsonRpcClient::VERSION_1,
            ),
            'http without port and path' => array(
                'address'                => 'http://valid_host',
                'version'                => '1.0',
                'expectedException'      => null,
                'expectedExceptionMsg'   => null,
                'expectedConnectionType' => AbstractConnection::CONN_HTTP,
                'expectedProtocol'       => Address::PROTOCOL_HTTP,
                'expectedHost'           => 'valid_host',
                'expectedPort'           => 80,
                'expectedPath'           => '/',
                'expectedVersion'        => JsonRpcClient::VERSION_1,
            ),

            'https'                       => array(
                'address'                => 'https://valid_host:8080/path',
                'version'                => '1.0',
                'expectedException'      => null,
                'expectedExceptionMsg'   => null,
                'expectedConnectionType' => AbstractConnection::CONN_HTTP,
                'expectedProtocol'       => Address::PROTOCOL_HTTPS,
                'expectedHost'           => 'valid_host',
                'expectedPort'           => 8080,
                'expectedPath'           => '/path',
                'expectedVersion'        => JsonRpcClient::VERSION_1,
            ),
            'https without port and path' => array(
                'address'                => 'https://valid_host',
                'version'                => '1.0',
                'expectedException'      => null,
                'expectedExceptionMsg'   => null,
                'expectedConnectionType' => AbstractConnection::CONN_HTTP,
                'expectedProtocol'       => Address::PROTOCOL_HTTPS,
                'expectedHost'           => 'valid_host',
                'expectedPort'           => 443,
                'expectedPath'           => '/',
                'expectedVersion'        => JsonRpcClient::VERSION_1,
            ),

            'ws'                       => array(
                'address'                => 'ws://valid_host:8080/path',
                'version'                => '1.0',
                'expectedException'      => null,
                'expectedExceptionMsg'   => null,
                'expectedConnectionType' => AbstractConnection::CONN_WS,
                'expectedProtocol'       => Address::PROTOCOL_WS,
                'expectedHost'           => 'valid_host',
                'expectedPort'           => 8080,
                'expectedPath'           => '/path',
                'expectedVersion'        => JsonRpcClient::VERSION_1,
            ),
            'ws without port and path' => array(
                'address'                => 'ws://valid_host',
                'version'                => '1.0',
                'expectedException'      => null,
                'expectedExceptionMsg'   => null,
                'expectedConnectionType' => AbstractConnection::CONN_WS,
                'expectedProtocol'       => Address::PROTOCOL_WS,
                'expectedHost'           => 'valid_host',
                'expectedPort'           => 80,
                'expectedPath'           => '/',
                'expectedVersion'        => JsonRpcClient::VERSION_1,
            ),

            'wss'                       => array(
                'address'                => 'wss://valid_host:8080/path',
                'version'                => '1.0',
                'expectedException'      => null,
                'expectedExceptionMsg'   => null,
                'expectedConnectionType' => AbstractConnection::CONN_WS,
                'expectedProtocol'       => Address::PROTOCOL_WSS,
                'expectedHost'           => 'valid_host',
                'expectedPort'           => 8080,
                'expectedPath'           => '/path',
                'expectedVersion'        => JsonRpcClient::VERSION_1,
            ),
            'wss without port and path' => array(
                'address'                => 'wss://valid_host',
                'version'                => '1.0',
                'expectedException'      => null,
                'expectedExceptionMsg'   => null,
                'expectedConnectionType' => AbstractConnection::CONN_WS,
                'expectedProtocol'       => Address::PROTOCOL_WSS,
                'expectedHost'           => 'valid_host',
                'expectedPort'           => 443,
                'expectedPath'           => '/',
                'expectedVersion'        => JsonRpcClient::VERSION_1,
            ),

            'mock'               => array(
                'address'                => 'mock://anything',
                'version'                => '1.0',
                'expectedException'      => null,
                'expectedExceptionMsg'   => null,
                'expectedConnectionType' => AbstractConnection::CONN_MOCK,
                'expectedProtocol'       => Address::PROTOCOL_MOCK,
                'expectedHost'           => 'anything',
                'expectedPort'           => null,
                'expectedPath'           => null,
                'expectedVersion'        => JsonRpcClient::VERSION_1,
            ),
            'mock with response' => array(
                'address'                => 'mock://{"result":"mocked result"}',
                'version'                => '1.0',
                'expectedException'      => null,
                'expectedExceptionMsg'   => null,
                'expectedConnectionType' => AbstractConnection::CONN_MOCK,
                'expectedProtocol'       => Address::PROTOCOL_MOCK,
                'expectedHost'           => '{"result":"mocked result"}',
                'expectedPort'           => null,
                'expectedPath'           => null,
                'expectedVersion'        => JsonRpcClient::VERSION_1,
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

        $client = new JsonRpcClient($address, $version);

        /** @var Address $address */
        $address = $this->getPrivatePropertyValue($client, 'address');

        $this->assertEquals($expectedProtocol, $address->protocol);
        $this->assertEquals($expectedHost, $address->host);
        $this->assertEquals($expectedPort, $address->port);
        $this->assertEquals($expectedPath, $address->path);
        $this->assertPrivatePropertyEquals($expectedConnectionType, $client, 'connectionType');
        $this->assertPrivatePropertyEquals($expectedVersion, $client, 'version');
    }

    public function testConnectionCreated()
    {
        $client = new JsonRpcClient('http://host/', JsonRpcClient::VERSION_1);
        $this->assertTrue($this->getPrivatePropertyValue($client, 'connection') instanceof Http);

        $client = new JsonRpcClient('https://host/', JsonRpcClient::VERSION_1);
        $this->assertTrue($this->getPrivatePropertyValue($client, 'connection') instanceof Http);

        $client = new JsonRpcClient('ws://host/', JsonRpcClient::VERSION_1);
        $this->assertTrue($this->getPrivatePropertyValue($client, 'connection') instanceof WebSocket);

        $client = new JsonRpcClient('wss://host/', JsonRpcClient::VERSION_1);
        $this->assertTrue($this->getPrivatePropertyValue($client, 'connection') instanceof WebSocket);

        $client = new JsonRpcClient('tcp://host:123/', JsonRpcClient::VERSION_1);
        $this->assertTrue($this->getPrivatePropertyValue($client, 'connection') instanceof Tcp);

        $client = new JsonRpcClient('host:123', JsonRpcClient::VERSION_1);
        $this->assertTrue($this->getPrivatePropertyValue($client, 'connection') instanceof Tcp);
    }

    public function testConnectionOptions()
    {
        $address = 'host:5555';
        $version = JsonRpcClient::VERSION_1;

        $expectedOptions = array(
            'key1' => 'value1',
            'key2' => 'value2',
        );

        $client     = new JsonRpcClient($address, $version, $expectedOptions);
        $connection = $this->getPrivatePropertyValue($client, 'connection');

        $this->assertTrue($connection instanceof Tcp);

        $actualOptions = $this->getPrivatePropertyValue($connection, 'options');

        $this->assertEquals($expectedOptions, $actualOptions);
    }

    /**
     * @param string $requestPattern
     * @param string $responseJson
     *
     * @return AbstractConnection|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createConnectionMock($requestPattern, $responseJson)
    {
        /** @var AbstractConnection|\PHPUnit_Framework_MockObject_MockObject $connection */
        $connection = $this
            ->getMockBuilder('\\JsonRpc\\Connection\\AbstractConnection')
            ->setMethods(array('send'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $connection
            ->expects($this->once())
            ->method('send')
            ->with(
                $this->matchesRegularExpression($requestPattern)
            )
            ->willReturnCallback(
                function ($request) use ($requestPattern, $responseJson) {
                    if (!empty($responseJson)) {
                        if (preg_match($requestPattern, $request, $matches)) {
                            array_shift($matches);
                            foreach ($matches as $id) {
                                $needle = '%ID%';
                                $pos    = strpos($responseJson, $needle);
                                if ($pos !== false) {
                                    $responseJson = substr_replace($responseJson, $id, $pos, strlen($needle));
                                }
                            }

                            return $responseJson;
                        }
                    }

                    return true;
                }
            );

        return $connection;
    }

    public function testSendRequestV1()
    {
        $address = 'host:5555';
        $version = JsonRpcClient::VERSION_1;
        $method  = 'testMethod';
        $params  = array(1, '2');

        $requestPattern = '/^\\{"method":"testMethod","params":\\[1,"2"\\],"id":("[\w-]+")\\}$/';
        $responseJson   = '{"result": 3, "error": null, "id": %ID%}';

        $connectionMock = $this->createConnectionMock($requestPattern, $responseJson);

        $client = new JsonRpcClient($address, $version);
        $this->setPrivatePropertyValue($client, 'connection', $connectionMock);

        $response = $client->sendRequest($method, $params);

        $this->assertTrue($response instanceof Response);
        $this->assertNull($response->error);
        $this->assertEquals(3, $response->result);
    }

    public function testSendRequestV1WithError()
    {
        $address = 'host:5555';
        $version = JsonRpcClient::VERSION_1;
        $method  = 'testMethod';
        $params  = array(1, '2');

        $requestPattern = '/^\\{"method":"testMethod","params":\\[1,"2"\\],"id":("[\w-]+")\\}$/';
        $responseJson   = '{"result":null,"error":{"code":-32601,"message":"Method not found"},"id":%ID%}';

        $connectionMock = $this->createConnectionMock($requestPattern, $responseJson);

        $client = new JsonRpcClient($address, $version);
        $this->setPrivatePropertyValue($client, 'connection', $connectionMock);

        $response = $client->sendRequest($method, $params);

        $this->assertTrue($response instanceof Response);
        $this->assertNull($response->result);
        $this->assertNotNull($response->error);
        $this->assertEquals(-32601, $response->error->code);
        $this->assertEquals('Method not found', $response->error->message);
    }

    public function testSendRequestV2()
    {
        $address = 'host:5555';
        $version = JsonRpcClient::VERSION_2;
        $method  = 'testMethod';
        $params  = array(1, '2');

        $requestPattern = '/^\\{"jsonrpc":"2.0","method":"testMethod","params":\\[1,"2"\\],"id":("[\w-]+")\\}$/';
        $responseJson   = '{"jsonrpc":"2.0","result": 3, "id": %ID%}';

        $connectionMock = $this->createConnectionMock($requestPattern, $responseJson);

        $client = new JsonRpcClient($address, $version);
        $this->setPrivatePropertyValue($client, 'connection', $connectionMock);

        $response = $client->sendRequest($method, $params);

        $this->assertTrue($response instanceof Response);
        $this->assertNull($response->error);
        $this->assertEquals(3, $response->result);
    }

    public function testSendRequestV2WithError()
    {
        $address = 'host:5555';
        $version = JsonRpcClient::VERSION_2;
        $method  = 'testMethod';
        $params  = array(1, '2');

        $requestPattern = '/^\\{"jsonrpc":"2.0","method":"testMethod","params":\\[1,"2"\\],"id":("[\w-]+")\\}$/';
        $responseJson   = '{"jsonrpc":"2.0","error":{"code":-32601,"message":"Method not found"},"id":%ID%}';

        $connectionMock = $this->createConnectionMock($requestPattern, $responseJson);

        $client = new JsonRpcClient($address, $version);
        $this->setPrivatePropertyValue($client, 'connection', $connectionMock);

        $response = $client->sendRequest($method, $params);

        $this->assertTrue($response instanceof Response);
        $this->assertNull($response->result);
        $this->assertNotNull($response->error);
        $this->assertEquals(-32601, $response->error->code);
        $this->assertEquals('Method not found', $response->error->message);
    }

    public function testSendBatchRequestV2()
    {
        $address  = 'host:5555';
        $version  = JsonRpcClient::VERSION_2;
        $requests = array(
            array('method01', array('param011', 'param012')),
            array('method02', array('param021', 'param022'), 55),
            array('method03', array('param031' => 'param031', 'param032' => 'param032')),
            array('method04'),
            array('method' => 'method05', 'params' => array('param051', 'param052'), 'id' => 999),
            new Request('method06', array('param061', 'param062')),
            new Request('method07', array('param071' => 'param071', 'param072' => 'param072')),
            new Request('method08'),
            new Notification('method09', array('param091', 'param092')),
            new Notification('method10', array('param101' => 'param101', 'param102' => 'param102')),
            new Notification('method11'),
        );

        $requestPattern = '/^\\[' .
                          '\\{"jsonrpc"\\:"2\\.0","method"\\:"method01","params"\\:\\["param011","param012"\\],"id"\\:("[\w-]+")\\},' .
                          '\\{"jsonrpc"\\:"2\\.0","method"\\:"method02","params"\\:\\["param021","param022"\\],"id"\\:55\\},' .
                          '\\{"jsonrpc"\\:"2\\.0","method"\\:"method03","params"\\:\\{"param031"\\:"param031","param032"\\:"param032"\\},"id"\\:("[\w-]+")\\},' .
                          '\\{"jsonrpc"\\:"2\\.0","method"\\:"method04","id"\\:("[\w-]+")\\},' .
                          '\\{"jsonrpc"\\:"2\\.0","method"\\:"method05","params"\\:\\["param051","param052"\\],"id"\\:999\\},' .
                          '\\{"jsonrpc"\\:"2\\.0","method"\\:"method06","params"\\:\\["param061","param062"\\],"id"\\:("[\w-]+")\\},' .
                          '\\{"jsonrpc"\\:"2\\.0","method"\\:"method07","params"\\:\\{"param071"\\:"param071","param072"\\:"param072"\\},"id"\\:("[\w-]+")\\},' .
                          '\\{"jsonrpc"\\:"2\\.0","method"\\:"method08","id"\\:("[\w-]+")\\},' .
                          '\\{"jsonrpc"\\:"2\\.0","method"\\:"method09","params"\\:\\["param091","param092"\\]\\},' .
                          '\\{"jsonrpc"\\:"2\\.0","method"\\:"method10","params"\\:\\{"param101"\\:"param101","param102"\\:"param102"\\}\\},' .
                          '\\{"jsonrpc"\\:"2\\.0","method"\\:"method11"\\}' .
                          '\\]$/';
        $responseJson   = '[' .
                          '{"jsonrpc":"2.0","result": "r1", "id": %ID%},' .
                          '{"jsonrpc":"2.0","result": "r2", "id": 55},' .
                          '{"jsonrpc":"2.0","result": "r3", "id": %ID%},' .
                          '{"jsonrpc":"2.0","result": "r4", "id": %ID%},' .
                          '{"jsonrpc":"2.0","error":{"code":-32601,"message":"Method not found"}, "id": 999},' .
                          '{"jsonrpc":"2.0","error":{"code":-32601,"message":"Method not found"}, "id": %ID%},' .
                          '{"jsonrpc":"2.0","error":{"code":-32601,"message":"Method not found"}, "id": %ID%},' .
                          '{"jsonrpc":"2.0","error":{"code":-32601,"message":"Method not found"}, "id": %ID%}' .
                          ']';

        $connectionMock = $this->createConnectionMock($requestPattern, $responseJson);

        $client = new JsonRpcClient($address, $version);
        $this->setPrivatePropertyValue($client, 'connection', $connectionMock);

        $responses = $client->sendBatchRequest($requests);

        $this->assertTrue(is_array($responses));
        for ($i = 0; $i < count($responses); $i++) {
            $response = $responses[$i];
            $this->assertTrue($response instanceof Response);

            if ($i == 1) {
                $this->assertEquals(55, $response->id);
            } elseif ($i == 4) {
                $this->assertEquals(999, $response->id);
            }

            if ($i < 4) {
                $this->assertNotNull($response->result);
                $this->assertNull($response->error);
                $this->assertEquals('r' . ($i + 1), $response->result);
            } else {
                $this->assertNull($response->result);
                $this->assertNotNull($response->error);
                $this->assertEquals(-32601, $response->error->code);
                $this->assertEquals('Method not found', $response->error->message);
            }
        }
    }

    public function testSendNotificationV1()
    {
        $address = 'host:5555';
        $version = JsonRpcClient::VERSION_1;
        $method  = 'testMethod';
        $params  = array(1, '2');

        $requestPattern = '/^\\{"method":"testMethod","params":\\[1,"2"\\],"id":null\\}$/';

        $connectionMock = $this->createConnectionMock($requestPattern, null);

        $client = new JsonRpcClient($address, $version);
        $this->setPrivatePropertyValue($client, 'connection', $connectionMock);

        $response = $client->sendNotification($method, $params);

        $this->assertTrue($response);
    }

    public function testSendNotificationV2()
    {
        $address = 'host:5555';
        $version = JsonRpcClient::VERSION_2;
        $method  = 'testMethod';
        $params  = array(1, '2');

        $requestPattern = '/^\\{"jsonrpc":"2.0","method":"testMethod","params":\\[1,"2"\\]\\}$/';

        $connectionMock = $this->createConnectionMock($requestPattern, null);

        $client = new JsonRpcClient($address, $version);
        $this->setPrivatePropertyValue($client, 'connection', $connectionMock);

        $response = $client->sendNotification($method, $params);

        $this->assertTrue($response);
    }

    public function testMockConnectionTypeV1()
    {
        $address = 'mock://localhost';
        $version = JsonRpcClient::VERSION_1;
        $method  = 'testMethod';
        $params  = array(1, '2');

        $client = new JsonRpcClient($address, $version);

        $response = $client->sendNotification($method, $params);
        $this->assertTrue($response);

        $response = $client->sendRequest($method, $params);
        $this->assertTrue($response->isSuccess());
        $this->assertTrue(isset($response->result->method));
        $this->assertTrue(isset($response->result->params));
        $this->assertEquals($method, $response->result->method);
        $this->assertEquals($params, $response->result->params);
    }

    public function testMockConnectionTypeV1MockedResult()
    {
        $expectedResult = 'mocked result';
        $address        = sprintf('mock://{"result":"%s"}', $expectedResult);
        $version        = JsonRpcClient::VERSION_1;
        $method         = 'testMethod';
        $params         = array(1, '2');

        $client = new JsonRpcClient($address, $version);

        $response = $client->sendNotification($method, $params);
        $this->assertTrue($response);

        $response = $client->sendRequest($method, $params);
        $this->assertTrue($response->isSuccess());
        $this->assertEquals($expectedResult, $response->result);
    }

    public function testMockConnectionTypeV1MockedError()
    {
        $expectedErrorCode = -12345;
        $expectedErrorMsg  = 'mocked error';
        $address           = sprintf(
            'mock://{"error":{"code":%d,"message":"%s"}}',
            $expectedErrorCode,
            $expectedErrorMsg
        );
        $version           = JsonRpcClient::VERSION_1;
        $method            = 'testMethod';
        $params            = array(1, '2');

        $client = new JsonRpcClient($address, $version);

        $response = $client->sendNotification($method, $params);
        $this->assertTrue($response);

        $response = $client->sendRequest($method, $params);
        $this->assertTrue($response->isError());
        $this->assertEquals($expectedErrorCode, $response->error->code);
        $this->assertEquals($expectedErrorMsg, $response->error->message);
    }

    public function testMockConnectionTypeV2()
    {
        $address = 'mock://localhost';
        $version = JsonRpcClient::VERSION_2;
        $method  = 'testMethod';
        $params  = array(1, '2');

        $client = new JsonRpcClient($address, $version);

        $response = $client->sendNotification($method, $params);
        $this->assertTrue($response);

        $response = $client->sendRequest($method, $params);
        $this->assertTrue($response->isSuccess());
        $this->assertTrue(isset($response->result->method));
        $this->assertTrue(isset($response->result->params));
        $this->assertEquals($method, $response->result->method);
        $this->assertEquals($params, $response->result->params);
    }

    public function testMockConnectionTypeV2MockedResult()
    {
        $expectedResult = 'mocked result';
        $address        = sprintf('mock://{"result":"%s"}', $expectedResult);
        $version        = JsonRpcClient::VERSION_2;
        $method         = 'testMethod';
        $params         = array(1, '2');

        $client = new JsonRpcClient($address, $version);

        $response = $client->sendNotification($method, $params);
        $this->assertTrue($response);

        $response = $client->sendRequest($method, $params);
        $this->assertTrue($response->isSuccess());
        $this->assertEquals($expectedResult, $response->result);
    }

    public function testMockConnectionTypeV2MockedError()
    {
        $expectedErrorCode = -12345;
        $expectedErrorMsg  = 'mocked error';
        $address           = sprintf(
            'mock://{"error":{"code":%d,"message":"%s"}}',
            $expectedErrorCode,
            $expectedErrorMsg
        );
        $version           = JsonRpcClient::VERSION_2;
        $method            = 'testMethod';
        $params            = array(1, '2');

        $client = new JsonRpcClient($address, $version);

        $response = $client->sendNotification($method, $params);
        $this->assertTrue($response);

        $response = $client->sendRequest($method, $params);
        $this->assertTrue($response->isError());
        $this->assertEquals($expectedErrorCode, $response->error->code);
        $this->assertEquals($expectedErrorMsg, $response->error->message);
    }
}
