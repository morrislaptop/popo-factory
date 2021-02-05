<?php

declare(strict_types=1);

namespace Morrislaptop\PopoFactory\Tests\Popos;

use Morrislaptop\PopoFactory\PopoFactory;

/**
 * @method PersonData make
 */
class PersonDataFactory extends PopoFactory
{
    public static function factory(): static
    {
        return static::new(PersonData::class)->state([
            'firstName' => 'Craig',
        ]);
    }

    public function gotNoJob()
    {
        return $this->state([
            'companyName' => null,
        ]);
    }

    public function worksAtHome()
    {
        return $this->state(function ($attributes) {
            return [
                'workAddress' => $attributes['homeAddress'],
            ];
        });
    }
}
