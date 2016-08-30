<?php

namespace JsonRpc\Connection;

class Mock extends AbstractConnection
{
    /**
     * @param string $payload
     *
     * @return string
     */
    public function send($payload)
    {
        $request  = json_decode($payload, true);
        $response = array();

        if (isset($request['id'])) {
            $response['id'] = $request['id'];
        }
        $response['result'] = array(
            'method' => $request['method'],
            'params' => $request['params'],
        );
        $response['error']  = null;

        return json_encode($response);
    }
}
