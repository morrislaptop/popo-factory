<?php

namespace Morrislaptop\PopoFactory\Tests\Popos;

use Carbon\Carbon;

class PersonData
{
    public function __construct(
        public $firstName,
        public string $lastName,
        public string $email,
        public string $homeAddress,
        public ?string $companyName,
        public string $workAddress,
        public Carbon $dob,
        public PersonDataDocBlock $spouse,
    ) {
    }
}
