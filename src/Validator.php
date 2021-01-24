<?php

namespace Morrislaptop\PopoFactory;

class Validator
{
    public static function isDTO(string $class): bool
    {
        if (! class_exists($class)) {
            return false;
        }

        return true;
    }
}
