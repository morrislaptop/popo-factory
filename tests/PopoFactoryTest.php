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
     * @covers \Morrislaptop\PopoFactory\Validator
     */
    public function test_new_factory_is_empty()
    {
        $countProp = $this->getProtectedProperty(
            PopoFactory::class,
            'count'
        );

        $collectionProp = $this->getProtectedProperty(
            PopoFactory::class,
            'collectionClass'
        );

        $dtoProp = $this->getProtectedProperty(
            PopoFactory::class,
            'dataTransferObjectClass'
        );

        $factory1 = PopoFactory::new()
                        ->dto(PersonData::class)
                        ->collection(PersonData::class)
                        ->count(5);

        $this->assertEquals($countProp->getValue($factory1), 5);
        $this->assertEquals($dtoProp->getValue($factory1), PersonData::class);
        $this->assertEquals($collectionProp->getValue($factory1), PersonData::class);

        $factory2 = $factory1::new();

        $this->expectErrorMessage('Typed property Morrislaptop\PopoFactory\PopoFactory::$count must not be accessed before initialization');
        $countProp->getValue($factory2);

        $this->expectErrorMessage('Typed property Morrislaptop\PopoFactory\PopoFactory::$collectionClass must not be accessed before initialization');
        $dtoProp->getValue($factory2);

        $this->expectErrorMessage('Typed property Morrislaptop\PopoFactory\PopoFactory::$PopoClass must not be accessed before initialization');
        $collectionProp->getValue($factory2);
    }

    /**
     * @covers \Morrislaptop\PopoFactory\PopoFactory
     */
    public function test_it_cannot_make_dto_if_dto_is_not_set()
    {
        $this->expectExceptionMessage('Please specify an Object to be generated!');
        PopoFactory::new()->make();
    }

    /**
     * @covers \Morrislaptop\PopoFactory\PopoFactory
     * @covers \Morrislaptop\PopoFactory\PropertyFactory
     * @covers \Morrislaptop\PopoFactory\Validator
     */
    public function test_it_can_make_single_dto()
    {
        $dto = PopoFactory::new()->dto(PersonData::class)->make();

        $this->assertInstanceOf(PersonData::class, $dto);
    }

    /**
     * @covers \Morrislaptop\PopoFactory\PopoFactory
     * @covers \Morrislaptop\PopoFactory\PropertyFactory
     * @covers \Morrislaptop\PopoFactory\Validator
     */
    public function test_it_can_make_array_of_dtos()
    {
        $dtos = PopoFactory::new()->dto(PersonData::class)->count(3)->make();

        $this->assertIsArray($dtos);

        foreach ($dtos as $dto) {
            $this->assertInstanceOf(PersonData::class, $dto);
        }
    }

    /**
     * @covers \Morrislaptop\PopoFactory\CollectionFactory
     * @covers \Morrislaptop\PopoFactory\PopoFactory
     * @covers \Morrislaptop\PopoFactory\PropertyFactory
     * @covers \Morrislaptop\PopoFactory\Validator
     */
    public function test_it_can_generate_dto_with_an_array_property()
    {
        $family = PopoFactory::new()
            ->dto(FamilyData::class)
            ->make();

        $this->assertInstanceOf(FamilyData::class, $family);

        $this->assertInstanceOf(PersonDataDocBlock::class, $family->person1);
        $this->assertInstanceOf(PersonDataDocBlock::class, $family->person2);

        foreach ($family->children as $child) {
            $this->assertInstanceOf(PersonData::class, $child);
        }
    }
}
