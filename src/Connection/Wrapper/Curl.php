<?php

namespace JsonRpc\Connection\Wrapper;

class Curl extends AbstractWrapper
{
    /**
     * @param string $url
     *
     * @return resource
     */
    public function init($url = null)
    {
        return curl_init($url);
    }

    /**
     * @param resource $handle
     */
    public function close($handle)
    {
        curl_close($handle);
    }

    /**
     * @param resource $handle
     *
     * @return mixed
     */
    public function exec($handle)
    {
        return curl_exec($handle);
    }

    /**
     * @param resource $handle
     * @param int      $option
     * @param mixed    $value
     *
     * @return bool
     */
    public function setopt($handle, $option, $value)
    {
        return curl_setopt($handle, $option, $value);
    }
}
