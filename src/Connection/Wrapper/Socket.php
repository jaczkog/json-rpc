<?php

namespace JsonRpc\Connection\Wrapper;

class Socket extends AbstractWrapper
{
    /**
     * @param string $hostname
     * @param int    $port    [optional]
     * @param int    $errno   [optional]
     * @param string $errstr  [optional]
     * @param float  $timeout [optional]
     *
     * @return resource
     */
    public function open($hostname, $port = null, &$errno = null, &$errstr = null, $timeout = null)
    {
        return pfsockopen($hostname, $port, $errno, $errstr, $timeout);
    }
    
    /**
     * @param resource $handle
     *
     * @return bool
     */
    public function close($handle)
    {
        return fclose($handle);
    }

    /**
     * @param resource $handle
     * @param string   $string
     * @param int      $length
     *
     * @return int
     */
    public function write($handle, $string, $length = null)
    {
        return fwrite($handle, $string, $length);
    }

    public function flush($handle)
    {
        return fflush($handle);
    }

    /**
     * @param resource $handle
     * @param int      $length
     *
     * @return string
     */
    public function read($handle, $length = null)
    {
        return fgets($handle, $length);
    }
}
