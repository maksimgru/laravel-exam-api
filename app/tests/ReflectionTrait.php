<?php

namespace Tests;

use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

trait ReflectionTrait
{
    /**
     * @throws ReflectionException
     */
    protected function getClassMethod(string $class, string $name): ReflectionMethod
    {
        $class = new ReflectionClass($class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);

        return $method;
    }

    /**
     * @throws ReflectionException
     */
    protected function callInternalMethod(object $object, string $name, array $arguments = []): mixed
    {
        $method = $this->getClassMethod(get_class($object), $name);

        return $method->invokeArgs($object, $arguments);
    }

    /**
     * @throws ReflectionException
     */
    protected function getNonPublicValue(object $object, string $name): mixed
    {
        $prop = (new ReflectionClass(get_class($object)))->getProperty($name);
        $prop->setAccessible(true);

        return $prop->getValue($object);
    }

    /**
     * @throws ReflectionException
     */
    protected function setNonPublicValue(object $object, string $name, $value): void
    {
        $prop = (new ReflectionClass(get_class($object)))->getProperty($name);
        $prop->setAccessible(true);
        $prop->setValue($object, $value);
    }
}
