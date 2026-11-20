<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Tests\Unit;

use ReflectionMethod;
use ReflectionProperty;

trait ReflectionTrait
{
    protected function createAccessibleMethod(object|string $class, string $method): ReflectionMethod
    {
        return new ReflectionMethod($class, $method);
    }

    protected function createAccessibleProperty(object|string $class, string $property): ReflectionProperty
    {
        return new ReflectionProperty($class, $property);
    }
}
