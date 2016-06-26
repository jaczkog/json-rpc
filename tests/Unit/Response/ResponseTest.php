<?php

namespace JsonRpc\Tests\Unit\Response;

use JsonRpc\Response\Error;
use JsonRpc\Response\Response;
use JsonRpc\Tests\Unit\AbstractTestCase;

class ResponseTest extends AbstractTestCase
{
    public function providerFromJson()
    {
        return array(
            'error - invalid json'          => array(
                'json'         => '{',
                'exception'    => '\\JsonRpc\\Exception\\InvalidResponseException',
                'result'       => null,
                'id'           => null,
                'errorCode'    => null,
                'errorMessage' => null,
            ),
            'error - invalid response'      => array(
                'json'         => '{"jsonrpc": "2.0", "result": 19}',
                'exception'    => '\\JsonRpc\\Exception\\InvalidResponseException',
                'result'       => null,
                'id'           => null,
                'errorCode'    => null,
                'errorMessage' => null,
            ),
            'error - invalid error'         => array(
                'json'         => '{"jsonrpc": "2.0", "result": null, "error": {"message": "Procedure not found."}, "id": 1}',
                'exception'    => '\\JsonRpc\\Exception\\InvalidResponseException',
                'result'       => null,
                'id'           => null,
                'errorCode'    => null,
                'errorMessage' => null,
            ),
            'error - no result and error'   => array(
                'json'         => '{"jsonrpc": "2.0", "result": null, "error": null, "id": 1}',
                'exception'    => '\\JsonRpc\\Exception\\InvalidResponseException',
                'result'       => null,
                'id'           => null,
                'errorCode'    => null,
                'errorMessage' => null,
            ),
            'error - both result and error' => array(
                'json'         => '{"jsonrpc": "2.0", "result": 1, "error": {"code": -32601, "message": "Procedure not found."}, "id": 1}',
                'exception'    => '\\JsonRpc\\Exception\\InvalidResponseException',
                'result'       => null,
                'id'           => null,
                'errorCode'    => null,
                'errorMessage' => null,
            ),
            'success with result'           => array(
                'json'         => '{"jsonrpc": "2.0", "result": 19, "id": 1}',
                'exception'    => null,
                'result'       => 19,
                'id'           => 1,
                'errorCode'    => null,
                'errorMessage' => null,
            ),
            'success with error'            => array(
                'json'         => '{"jsonrpc": "2.0", "error": {"code": -32601, "message": "Procedure not found."}, "id": 10}',
                'exception'    => null,
                'result'       => null,
                'id'           => 10,
                'errorCode'    => -32601,
                'errorMessage' => 'Procedure not found.',
            ),
            'success with batch response'   => array(
                'json'         => '[' .
                                  '{"jsonrpc":"2.0","result":7,"id":"1"},' .
                                  '{"jsonrpc":"2.0","result":19,"id":"2"},' .
                                  '{"jsonrpc":"2.0","error":{"code":-32600,"message":"Invalid Request"},"id":null},' .
                                  '{"jsonrpc":"2.0","error":{"code":-32601,"message":"Method not found"},"id":"5"},' .
                                  '{"jsonrpc":"2.0","result":["hello",5],"id":"9"}' .
                                  ']',
                'exception'    => null,
                'result'       => array(7, 19, null, null, array('hello', 5)),
                'id'           => array(1, 2, null, 5, 9),
                'errorCode'    => array(null, null, -32600, -32601, null),
                'errorMessage' => array(null, null, 'Invalid Request', 'Method not found', null),
            ),
        );
    }

    /**
     * @param string $json
     * @param string $expectedException
     * @param mixed  $result
     * @param mixed  $id
     * @param int    $errorCode
     * @param string $errorMessage
     *
     * @throws \JsonRpc\Exception\InvalidResponseException
     * @dataProvider providerFromJson
     */
    public function testFromJson($json, $expectedException, $result, $id, $errorCode, $errorMessage)
    {
        if (!empty($expectedException)) {
            $this->setExpectedException($expectedException);
        }

        $response = Response::fromJson($json);

        if (is_array($response)) {
            for ($i = 0; $i < count($response); $i++) {
                $this->runResponseAssertions($response[$i], $result[$i], $id[$i], $errorCode[$i], $errorMessage[$i]);
            }
        } else {
            $this->runResponseAssertions($response, $result, $id, $errorCode, $errorMessage);
        }
    }

    /**
     * @param Response $response
     * @param mixed    $result
     * @param mixed    $id
     * @param int      $errorCode
     * @param string   $errorMessage
     */
    private function runResponseAssertions(Response $response, $result, $id, $errorCode, $errorMessage)
    {
        $this->assertEquals($result, $response->result);
        $this->assertEquals($id, $response->id);
        if (isset($errorCode)) {
            $this->assertTrue($response->error instanceof Error);
            $this->assertEquals($errorCode, $response->error->code);
            $this->assertEquals($errorMessage, $response->error->message);
        } else {
            $this->assertNull($response->error);
        }
    }
}
