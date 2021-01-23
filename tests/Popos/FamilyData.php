<?php

namespace Morrislaptop\PopoFactory\Tests\Popos;

use Spatie\Popo\Popo;
use Morrislaptop\PopoFactory\Tests\Popos\PersonData;

class FamilyData
{
    public function __construct(
        public PersonDataDocBlock $person1,
        public PersonDataDocBlock $person2,
        /** @var \Morrislaptop\PopoFactory\Tests\Popos\PersonData[] */
        public $children,
    ) { }
}
