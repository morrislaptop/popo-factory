<?php

namespace Morrislaptop\PopoFactory\Tests\Popos;

class PersonDataDocBlock
{
    public function __construct(
        public $firstName,

        /** @var string */
        public $lastName,

        /** @var string */
        public $email,

        /** @var string */
        public $homeAddress,

        /** @var string */
        public $companyName,

        /** @var string */
        public $workAddress,
    ) {
    }
}
