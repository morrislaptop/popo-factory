<?php

namespace Morrislaptop\PopoFactory\Tests;

use Morrislaptop\PopoFactory\PopoFactory;
use Morrislaptop\PopoFactory\Tests\Popos\FamilyData;
use Morrislaptop\PopoFactory\Tests\Popos\PersonData;
use Morrislaptop\PopoFactory\Tests\Popos\PersonDataDocBlock;

class PopoFactoryTest extends AbstractTestCase
{
    /**
     * @covers \Morrislaptop\PopoFactory\PopoFactory
     * @covers \Morrislaptop\PopoFactory\PropertyFactory
     * @covers \Morrislaptop\PopoFactory\Validator
     */
    public function test_it_can_make_single_dto()
    {
        $dto = PopoFactory::new(PersonData::class)->make();

        $this->assertInstanceOf(PersonData::class, $dto);
    }

    /**
     * @covers \Morrislaptop\PopoFactory\PopoFactory
     * @covers \Morrislaptop\PopoFactory\PropertyFactory
     * @covers \Morrislaptop\PopoFactory\Validator
     */
    public function test_it_can_make_array_of_dtos()
    {
        $dtos = PopoFactory::new(PersonData::class)->count(3)->make();

        $this->assertIsArray($dtos);

        foreach ($dtos as $dto) {
            $this->assertInstanceOf(PersonData::class, $dto);
        }
    }

    /**
     * @covers \Morrislaptop\PopoFactory\PopoFactory
     * @covers \Morrislaptop\PopoFactory\PropertyFactory
     * @covers \Morrislaptop\PopoFactory\Validator
     */
    public function test_it_can_generate_dto_with_an_array_property()
    {
        $family = PopoFactory::new(FamilyData::class)
            ->make();

        $this->assertInstanceOf(FamilyData::class, $family);

        $this->assertInstanceOf(PersonDataDocBlock::class, $family->person1);
        $this->assertInstanceOf(PersonDataDocBlock::class, $family->person2);

        foreach ($family->children as $child) {
            $this->assertInstanceOf(PersonData::class, $child);
        }
    }
}
