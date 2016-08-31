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

        $response = json_decode($this->address->host, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $response = array();
            $response['result'] = array(
                'method' => $request['method'],
                'params' => $request['params'],
            );
            $response['error']  = null;
        }

        if (isset($request['id'])) {
            $response['id'] = $request['id'];
        }

        return json_encode($response);
    }
}
