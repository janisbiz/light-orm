<?php

namespace Janisbiz\LightOrm\Tests\Unit;

trait ReflectionTrait
{
    /**
     * @param object|string $class
     * @param string $method
     *
     * @return \ReflectionMethod
     */
    protected function createAccessibleMethod($class, $method)
    {
        $reflectionMethod = new \ReflectionMethod($class, $method);
        $reflectionMethod->setAccessible(true);

        return $reflectionMethod;
    }

    /**
     * @param object|string $class
     * @param string $property
     *
     * @return \ReflectionProperty
     */
    protected function createAccessibleProperty($class, $property)
    {
        $reflectionProperty = new \ReflectionProperty($class, $property);
        $reflectionProperty->setAccessible(true);

        return $reflectionProperty;
    }
}
