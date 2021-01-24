<?php

namespace Morrislaptop\PopoFactory;

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
