<?php

namespace Morrislaptop\PopoFactory\Tests\Popos;

class PersonData
{
    public function __construct(
        public $firstName,
        public string $lastName,
        public string $email,
        public string $homeAddress,
        public string $companyName,
        public string $workAddress,
        public PersonDataDocBlock $spouse,
    ) {
    }
}
