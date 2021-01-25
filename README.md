# Making it easy to mock your POPO's

[![Latest Version on Packagist](https://img.shields.io/packagist/v/morrislaptop/popo-factory.svg?style=flat-square)](https://packagist.org/packages/morrislaptop/popo-factory)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/morrislaptop/popo-factory/Tests?label=tests)](https://github.com/morrislaptop/popo-factory/actions?query=workflow%3ATests+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/morrislaptop/popo-factory.svg?style=flat-square)](https://packagist.org/packages/morrislaptop/popo-factory)

This package supports mocking POPO. This is a fork of [Data Transfer Object Factory](https://github.com/anteris-dev/data-transfer-object-factory) which supports normal 
PHP Objects instead of DTO's.

## Installation

You can install the package via composer:

```bash
composer require morrislaptop/popo-factory --dev
```

## Usage

If you are simply using PHP default types in your DTOs, you can get started right away. Just pass your DTO FQDN to the static dto method. Calling this method on the factory returns an instance of `Morrislaptop\PopoFactory\PopoFactory` which provides the following methods.

- `count()` - _Allows you to specify how many DTOs to be generated. They will be returned in an array._
- `make()` - _Called when you are ready to generate the DTO. Returns the generated DTO._
- `random()` - _Generates a random number of DTOs_
- `sequence()` - _Alternates a specific state. (See below)_
- `state()` - _Manually sets properties based on the array of values passed._

Examples of these methods can be found below.

```php

use Morrislaptop\PopoFactory\PopoFactory;

// Creates one DTO
PopooFactory::new(PersonData::class)->make();

// Creates two DTOs in an array
PopoFactory::new(PersonData::class)->count(2)->make();

// Sets the first name of every person to "Jim"
PopoFactory::new(PersonData::class)
    ->random()
    ->state([
        'firstName' => 'Jim',
    ])
    ->make();

// Alternates the names of each person between "Jim" and "Susie"
PopoFactory::new(PersonData::class)
    ->random()
    ->sequence(
        [ 'firstName' => 'Jim' ],
        [ 'firstName' => 'Susie' ]
    )
    ->make();

```

## Creating Class Based Factories

It's useful to define specific factories for particular objects, which can easily be done by extending the `PopoFactory` class. 

My specifying a typehint for the `make()` method you will also get typehints in your IDE for your mocked object.

```php
<?php

/**
 * @method PersonData make
 */
class PersonDataFactory extends PopoFactory
{
    public static function factory(): static
    {
        return static::new(PersonData::class)->state([
            'firstName' => 'Craig'
        ]);
    }

    public function gotNoJob() {
        return $this->state([
            'companyName' => null,
        ]);
    }

    public function worksAtHome() {
        return $this->state(function ($attributes) {
            return [
                'workAddress' => $attributes['homeAddress']
            ];
        });
    }
}
```

Then using it in tests like so:

```php
$person = PersonDataFactory::factory()
            ->gotNoJob()
            ->worksAtHome()
            ->make();
```


## Extending

You can easily extend the factory to support other data types. You can do this through the static `registerProvider()` method on the `PropertyFactory` class. This method takes two arguments. The first should be the FQDN of the class you are providing (e.g. `Carbon\Carbon`) OR the built-in type (e.g. `string`). The second should be a callback that returns the generated value. This callback is passed two properties when called to assist in generating the value. The first is an instance of `Anteris\FakerMap\FakerMap` which can be used to help generate fake data. The second is an instance of `ReflectionProperty` which contains information about the property being generated.

For example, to support Carbon:

```php

use Morrislaptop\PopoFactory\PropertyFactory;

use Anteris\FakerMap\FakerMap;

PropertyFactory::registerProvider('Carbon\Carbon', fn(FakerMap $fakerMap) => Carbon::parse(
    $fakerMap->closest('dateTime')->fake()
));

```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Craig Morris](https://github.com/morrislaptop)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
