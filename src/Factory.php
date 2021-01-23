<?php

namespace Morrislaptop\PopoFactory;

class Factory
{
    public static function dto(string $dto): PopoFactory
    {
        return PopoFactory::new()->dto($dto);
    }
}
