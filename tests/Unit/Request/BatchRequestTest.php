<?php

namespace JsonRpc\Tests\Unit\Request;

use JsonRpc\JsonRpc;
use JsonRpc\Request\BatchRequest;
use JsonRpc\Request\Notification;
use JsonRpc\Request\Request;

class BatchRequestTest extends \PHPUnit_Framework_TestCase
{
    public function testJson()
    {
        $request = new BatchRequest(
            array(
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
            )
        );

        $this->assertRegExp(
            '/^\\[' .
            '\\{"method"\\:"method01","params"\\:\\["param011","param012"\\],"id"\\:"[\w-]+"\\},' .
            '\\{"method"\\:"method02","params"\\:\\["param021","param022"\\],"id"\\:55\\},' .
            '\\{"method"\\:"method03","params"\\:\\["param031","param032"\\],"id"\\:"[\w-]+"\\},' .
            '\\{"method"\\:"method04","params"\\:\\[\\],"id"\\:"[\w-]+"\\},' .
            '\\{"method"\\:"method05","params"\\:\\["param051","param052"\\],"id"\\:999\\},' .
            '\\{"method"\\:"method06","params"\\:\\["param061","param062"\\],"id"\\:"[\w-]+"\\},' .
            '\\{"method"\\:"method07","params"\\:\\["param071","param072"\\],"id"\\:"[\w-]+"\\},' .
            '\\{"method"\\:"method08","params"\\:\\[\\],"id"\\:"[\w-]+"\\},' .
            '\\{"method"\\:"method09","params"\\:\\["param091","param092"\\],"id"\\:null\\},' .
            '\\{"method"\\:"method10","params"\\:\\["param101","param102"\\],"id"\\:null\\},' .
            '\\{"method"\\:"method11","params"\\:\\[\\],"id"\\:null\\}' .
            '\\]$/',
            $request->toJson(JsonRpc::VER_1)
        );

        $this->assertRegExp(
            '/^\\[' .
            '\\{"jsonrpc"\\:"2\\.0","method"\\:"method01","params"\\:\\["param011","param012"\\],"id"\\:"[\w-]+"\\},' .
            '\\{"jsonrpc"\\:"2\\.0","method"\\:"method02","params"\\:\\["param021","param022"\\],"id"\\:55\\},' .
            '\\{"jsonrpc"\\:"2\\.0","method"\\:"method03","params"\\:\\{"param031"\\:"param031","param032"\\:"param032"\\},"id"\\:"[\w-]+"\\},' .
            '\\{"jsonrpc"\\:"2\\.0","method"\\:"method04","id"\\:"[\w-]+"\\},' .
            '\\{"jsonrpc"\\:"2\\.0","method"\\:"method05","params"\\:\\["param051","param052"\\],"id"\\:999\\},' .
            '\\{"jsonrpc"\\:"2\\.0","method"\\:"method06","params"\\:\\["param061","param062"\\],"id"\\:"[\w-]+"\\},' .
            '\\{"jsonrpc"\\:"2\\.0","method"\\:"method07","params"\\:\\{"param071"\\:"param071","param072"\\:"param072"\\},"id"\\:"[\w-]+"\\},' .
            '\\{"jsonrpc"\\:"2\\.0","method"\\:"method08","id"\\:"[\w-]+"\\},' .
            '\\{"jsonrpc"\\:"2\\.0","method"\\:"method09","params"\\:\\["param091","param092"\\]\\},' .
            '\\{"jsonrpc"\\:"2\\.0","method"\\:"method10","params"\\:\\{"param101"\\:"param101","param102"\\:"param102"\\}\\},' .
            '\\{"jsonrpc"\\:"2\\.0","method"\\:"method11"\\}' .
            '\\]$/',
            $request->toJson(JsonRpc::VER_2)
        );
    }

    public function testInvalidRequests()
    {
        $this->setExpectedException(
            '\\JsonRpc\\Exception\\InvalidRequestException',
            sprintf('Invalid request: "%s"', var_export(array('method2' => 'method2'), true))
        );

        new BatchRequest(
            array(
                array('method1'),
                array('method2' => 'method2'),
            )
        );
    }
}
