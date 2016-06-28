<?php

namespace JsonRpc\Request;

class Notification extends Request
{
    /**
     * Notification constructor.
     *
     * @param string $method
     * @param array  $params
     */
    public function __construct($method, array $params = array())
    {
        parent::__construct($method, $params, null);
    }
}
