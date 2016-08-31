<?php

namespace JsonRpc\Tests\Unit\Common;

use JsonRpc\Common\Address;

class AddressTest extends \PHPUnit_Framework_TestCase
{
    public function providerAddressToString()
    {
        return array(
            array(
                'string' => 'tcp://host1:5555',
                'protocol' => 'tcp',
                'host'     => 'host1',
                'port'     => 5555,
                'path'     => null,
            ),
            array(
                'string' => 'http://host2:80/',
                'protocol' => 'http',
                'host'     => 'host2',
                'port'     => 80,
                'path'     => '/',
            ),
            array(
                'string' => 'https://host3:443/app',
                'protocol' => 'https',
                'host'     => 'host3',
                'port'     => 443,
                'path'     => '/app',
            ),
            array(
                'string' => 'ws://host4:80/',
                'protocol' => 'ws',
                'host'     => 'host4',
                'port'     => 80,
                'path'     => '/',
            ),
            array(
                'string' => 'wss://host5:443/app',
                'protocol' => 'wss',
                'host'     => 'host5',
                'port'     => 443,
                'path'     => '/app',
            ),
            array(
                'string' => 'mock://{"result":"banana"}',
                'protocol' => 'mock',
                'host'     => '{"result":"banana"}',
                'port'     => null,
                'path'     => null,
            ),
        );
    }

    /**
     * @param string $string
     * @param string $protocol
     * @param string $host
     * @param int    $port
     * @param string $path
     *
     * #@dataProvider providerAddressToString
     */
    public function testParse($string, $protocol, $host, $port, $path)
    {
        $address = Address::parse($string);

        $this->assertEquals($protocol, $address->protocol);
        $this->assertEquals($host, $address->host);
        $this->assertEquals($port, $address->port);
        $this->assertEquals($path, $address->path);
    }

    /**
     * @param string $string
     * @param string $protocol
     * @param string $host
     * @param int    $port
     * @param string $path
     *
     * #@dataProvider providerAddressToString
     */
    public function testAddressToString($string, $protocol, $host, $port, $path)
    {
        $address = new Address($protocol, $host, $port, $path);

        $this->assertEquals($string, (string)$address);
    }
}
