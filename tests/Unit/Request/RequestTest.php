<?php

namespace JsonRpc\Tests\Unit\Request;

use JsonRpc\JsonRpc;
use JsonRpc\Request\Request;
use JsonRpc\Tests\Unit\AbstractTestCase;

class RequestTest extends AbstractTestCase
{
    public function testJson()
    {
        $request = new Request('testMethod', array(1, 'value2'));

        $this->assertRegExp(
            '/^\\{"method":"testMethod","params":\\[1,"value2"\\],"id":"[\w-]+"\\}$/',
            $request->toJson(JsonRpc::VER_1)
        );

        $this->assertRegExp(
            '/^\\{"jsonrpc":"2.0","method":"testMethod","params":\\[1,"value2"\\],"id":"[\w-]+"\\}$/',
            $request->toJson(JsonRpc::VER_2)
        );
    }

    public function testJsonWithNoParams()
    {
        $request = new Request('testMethod2');

        $this->assertRegExp(
            '/^\\{"method":"testMethod2","params":\\[\\],"id":"[\w-]+"\\}$/',
            $request->toJson(JsonRpc::VER_1)
        );

        $this->assertRegExp(
            '/^\\{"jsonrpc":"2.0","method":"testMethod2","id":"[\w-]+"\\}$/',
            $request->toJson(JsonRpc::VER_2)
        );
    }

    public function testJsonWithNamedParams()
    {
        $request = new Request('testMethod3', array('param1' => 1, 'param2' => 'value2'));

        $this->assertRegExp(
            '/^\\{"method":"testMethod3","params":\\[1,"value2"\\],"id":"[\w-]+"\\}$/',
            $request->toJson(JsonRpc::VER_1)
        );

        $this->assertRegExp(
            '/^\\{"jsonrpc":"2.0","method":"testMethod3","params":\\{"param1":1,"param2":"value2"\\},"id":"[\w-]+"\\}$/',
            $request->toJson(JsonRpc::VER_2)
        );
    }

    public function testRequestHaveRandomId()
    {
        $ids = array();

        for ($i = 0; $i < 100; $i++) {
            $request = new Request('testMethod', array(1, 'value2'));
            $id      = $this->getPrivatePropertyValue($request, 'id');
            $this->assertNotContains($id, $ids);
            $ids[] = $id;
        }
    }

    public function testRequestCanHaveCustomId()
    {
        $id      = uniqid() . '-' . microtime(true);
        $request = new Request('testMethod', array(1, 'value2'), $id);

        $this->assertEquals($id, $this->getPrivatePropertyValue($request, 'id'));
        $this->assertEquals(sprintf('{"method":"testMethod","params":[1,"value2"],"id":"%s"}', $id), $request->toJson());
    }

    public function testRequestToString()
    {
        $request = new Request('testMethod', array(1, 'value2'));

        $this->assertEquals($request->toJson(), (string)$request);
    }
}
