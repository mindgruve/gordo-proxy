<?php

namespace Mindgruve\Gordo\Proxy;

use Doctrine\Common\Inflector\Inflector;

class PropertyAccess
{
    public static function set($obj, $prop, $value)
    {
        $reflectionClass = new \ReflectionClass($obj);

        $setter = Inflector::camelize('set_' . $prop);
        if (method_exists($obj, $setter)) {
            return $obj->$setter($value);
        }

        $reflectionProperty = $reflectionClass->getProperty($prop);
        if ($reflectionProperty->isPrivate() || $reflectionProperty->isProtected()) {
            $reflectionProperty->setAccessible(true);
            $reflectionProperty->setValue($obj, $value);
            $reflectionProperty->setAccessible(false);
        } else {
            $reflectionProperty->setValue($obj, $value);
        }

        return $obj;
    }

    public static function get($obj, $prop)
    {
        $reflectionClass = new \ReflectionClass($obj);

        $getter = Inflector::camelize('get_' . $prop);
        if (method_exists($obj, $getter)) {
            return $obj->$getter();
        }

        $reflectionProperty = $reflectionClass->getProperty($prop);

        if ($reflectionProperty->isPrivate() || $reflectionProperty->isProtected()) {
            $reflectionProperty->setAccessible(true);
            $value = $reflectionProperty->getValue($obj);
            $reflectionProperty->setAccessible(false);
        } else {
            $value = $reflectionProperty->getValue($obj);
        }

        return $value;
    }
}