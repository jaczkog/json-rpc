<?php

namespace JsonRpc\Tests\Unit\Common;

use JsonRpc\Common\Address;

class AddressTest extends \PHPUnit_Framework_TestCase
{
    public function providerAddressToString()
    {
        return array(
            array(
                'protocol' => 'tcp',
                'host'     => 'host1',
                'port'     => 5555,
                'path'     => null,
                'expected' => 'tcp://host1:5555',
            ),
            array(
                'protocol' => 'http',
                'host'     => 'host2',
                'port'     => 80,
                'path'     => '/',
                'expected' => 'http://host2:80/',
            ),
            array(
                'protocol' => 'https',
                'host'     => 'host3',
                'port'     => 443,
                'path'     => '/app',
                'expected' => 'https://host3:443/app',
            ),
            array(
                'protocol' => 'ws',
                'host'     => 'host4',
                'port'     => 80,
                'path'     => '/',
                'expected' => 'ws://host4:80/',
            ),
            array(
                'protocol' => 'wss',
                'host'     => 'host5',
                'port'     => 443,
                'path'     => '/app',
                'expected' => 'wss://host5:443/app',
            ),
        );
    }

    /**
     * @param string $protocol
     * @param string $host
     * @param int    $port
     * @param string $path
     * @param string $expected
     *
     * #@dataProvider providerAddressToString
     */
    public function testAddressToString($protocol, $host, $port, $path, $expected)
    {
        $address = new Address($protocol, $host, $port, $path);

        $this->assertEquals($expected, (string)$address);
    }
}
