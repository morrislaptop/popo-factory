<?php

namespace Morrislaptop\PopoFactory\Tests\Popos;

use Spatie\Popo\Popo;
use Morrislaptop\PopoFactory\Tests\Popos\PersonData;

class FamilyData
{
    public function __construct(
        public PersonDataDocBlock $person1,
        /** @var PersonDataDocBlock */
        public $person2,
        /** @var \Morrislaptop\PopoFactory\Tests\Popos\PersonData */
        public $person3,
        /** @var PersonData[] */
        public $children,
    ) { }
}
