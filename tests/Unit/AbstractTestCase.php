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
            $result = $reflectionMethod->invokeArgs($reflectionMethod->isStatic() ? null : $object, $args);
            $reflectionMethod->setAccessible(false);

            return $result;
        } else {
            return call_user_func_array(array($object, $method), $args);
        }
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
            $value = $reflectionProperty->getValue($object);
            $reflectionProperty->setAccessible(false);

            return $value;
        } else {
            return $object->$property;
        }
    }

    /**
     * @param object $object
     * @param string $property
     * @param mixed  $value
     */
    protected function setPrivatePropertyValue($object, $property, $value)
    {
        $reflectionProperty = new \ReflectionProperty($object, $property);
        if ($reflectionProperty->isPrivate() || $reflectionProperty->isProtected()) {
            $reflectionProperty->setAccessible(true);
            $reflectionProperty->setValue($object, $value);
            $reflectionProperty->setAccessible(false);
        } else {
            $object->$property = $value;
        }
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
