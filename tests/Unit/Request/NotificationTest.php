<?php

namespace JsonRpc\Tests\Unit\Request;

use JsonRpc\JsonRpc;
use JsonRpc\Request\Notification;
use JsonRpc\Tests\Unit\AbstractTestCase;

class NotificationTest extends AbstractTestCase
{
    public function testRequestJson()
    {
        $request = new Notification('testMethod', array(1, 'value2'));

        $this->assertEquals(
            '{"method":"testMethod","params":[1,"value2"],"id":null}',
            $request->toJson(JsonRpc::VER_1)
        );

        $this->assertEquals(
            '{"jsonrpc":"2.0","method":"testMethod","params":[1,"value2"]}',
            $request->toJson(JsonRpc::VER_2)
        );
    }

    public function testRequestJsonV2NoParams()
    {
        $request = new Notification('testMethodNoParams');

        $this->assertEquals('{"method":"testMethodNoParams","params":[],"id":null}', $request->toJson(JsonRpc::VER_1));

        $this->assertEquals('{"jsonrpc":"2.0","method":"testMethodNoParams"}', $request->toJson(JsonRpc::VER_2));
    }

    public function testRequestJsonWithNamedParams()
    {
        $request = new Notification('testMethod3', array('param1' => 1, 'param2' => 'value2'));

        $this->assertEquals(
            '{"method":"testMethod3","params":[1,"value2"],"id":null}',
            $request->toJson(JsonRpc::VER_1)
        );

        $this->assertEquals(
            '{"jsonrpc":"2.0","method":"testMethod3","params":{"param1":1,"param2":"value2"}}',
            $request->toJson(JsonRpc::VER_2)
        );
    }

}
