<?php

namespace JsonRpc\Tests\Unit;

class AbstractTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @param object $object
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    protected function callPrivateMethod($object, $method, $args = array())
    {
        $reflectionMethod = new \ReflectionMethod($object, $method);
        if ($reflectionMethod->isPrivate() || $reflectionMethod->isProtected()) {
            $reflectionMethod->setAccessible(true);
        }

        return $reflectionMethod->invokeArgs($reflectionMethod->isStatic() ? null : $object, $args);
    }

    /**
     * @param object $object
     * @param string $property
     *
     * @return mixed
     */
    protected function getPrivatePropertyValue($object, $property)
    {
        $reflectionProperty = new \ReflectionProperty($object, $property);
        if ($reflectionProperty->isPrivate() || $reflectionProperty->isProtected()) {
            $reflectionProperty->setAccessible(true);
        }

        return $reflectionProperty->getValue($object);
    }

    /**
     * @param mixed  $expected
     * @param mixed  $object
     * @param string $property
     * @param string $message
     */
    protected function assertPrivatePropertyEquals($expected, $object, $property, $message = '')
    {
        $this->assertEquals($expected, $this->getPrivatePropertyValue($object, $property), $message);
    }
}
