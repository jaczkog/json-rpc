<?php

namespace JsonRpc\Connection\Wrapper;

class AbstractWrapper
{
    /**
     * @param mixed $handle
     *
     * @return bool
     */
    public function isConnected($handle)
    {
        return is_resource($handle);
    }
}
