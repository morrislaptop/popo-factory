<?php

namespace Morrislaptop\PopoFactory;

use ReflectionClass;
use Spatie\DataTransferObject\DataTransferObject;

class Validator
{
    public static function isDTO(string $class)
    {
        if (! class_exists($class)) {
            return false;
        }

        return true;
    }
}
