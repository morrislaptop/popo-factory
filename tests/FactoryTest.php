<?php

namespace Morrislaptop\PopoFactory\Tests;

use Morrislaptop\PopoFactory\Factory;
use Morrislaptop\PopoFactory\PopoFactory;
use Morrislaptop\PopoFactory\Tests\Popos\PersonData;
use PHPUnit\Framework\TestCase;

class FactoryTest extends TestCase
{
    /**
     * @covers \Morrislaptop\PopoFactory\Factory
     * @covers \Morrislaptop\PopoFactory\PopoFactory
     * @covers \Morrislaptop\PopoFactory\Validator
     */
    public function test_dto_returns_dto_factory()
    {
        $factory = Factory::dto(PersonData::class);

        $this->assertInstanceOf(PopoFactory::class, $factory);
    }
}
