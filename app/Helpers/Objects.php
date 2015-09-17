<?php

namespace App\Helpers;

class Objects
{
    public static function toArray($class)
    {
        $reflectionClass = new \ReflectionClass(get_class($class));
        $array = [];
        foreach ($reflectionClass->getProperties() as $property) {
            $property->setAccessible(true);
            $array[$property->getName()] = $property->getValue($class);
            $property->setAccessible(false);
        }

        return $array;
    }
}
