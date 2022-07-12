<?php

namespace Morrislaptop\PopoFactory;

use Anteris\FakerMap\FakerMap;
use ReflectionNamedType;
use ReflectionProperty;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;

class PropertyFactory
{
    protected PhpDocExtractor $phpDocExtractor;
    protected static array $providers = [];
    private FakerMap $fakerMap;

    public function __construct(?FakerMap $fakerMap = null)
    {
        $this->phpDocExtractor = new PhpDocExtractor();
        $this->fakerMap = $fakerMap ?? FakerMap::new();
    }

    public static function new(): self
    {
        return new self;
    }

    public static function registerProvider(string $name, callable $callback): void
    {
        static::$providers[$name] = $callback;
    }

    public function make(ReflectionProperty $property)
    {
        $type = $this->extractType($property);

        // If a provider was registered to handle this type, pass off to that.
        if (isset(static::$providers[$type])) {
            return static::$providers[$type](FakerMap::new(), $property);
        }

        // We will try to generate a property that matches what the property name
        // indicates it expects. (e.g. $firstName would have "John")
        $faker = $this->fakerMap->closest($property->getName());

        // If the property was type cast, we will ensure the returned type is
        // what the property expects. Otherwise we will fallback on a value based
        // on the type.
        if ($type != null) {
            $faker = $faker->type($type)->default(
                $this->createPropertyOfType($type)
            );
        }

        // If the property did not have a type, we will fallback on a random
        // type.
        if ($type == null) {
            $faker = $faker->default($this->createPropertyOfRandomType());
        }

        return $faker->fake();
    }

    protected function extractType(ReflectionProperty $property): ?string
    {
        if (
            $property->getDocComment() &&
            $type = $this->extractDocBlockType($property)
        ) {
            return $type;
        }

        $type = $property->getType();

        if ($type && $type instanceof ReflectionNamedType) {
            return $type->getName();
        }

        return null;
    }

    protected function extractDocBlockType(ReflectionProperty $property): ?string
    {
        $types = $this->phpDocExtractor->getTypes(
            $property->class,
            $property->name,
        );

        if ($types) {
            // Picks a random type out of the options.
            $type = $types[array_rand($types, 1)];

            if ($type->isCollection()) {
                // Patch for Symfony 6
                if (method_exists($type,'getCollectionValueTypes')) {
                    $collectionType = $type->getCollectionValueTypes()[0];
                } else {
                    $collectionType = $type->getCollectionValueType();
                }
                $className = $collectionType->getClassName() ?? $collectionType->getBuiltinType();

                return $className . '[]';
            }

            return $type->getClassName() ?? $type->getBuiltinType();
        }

        return null;
    }

    /**
     * @param class-string $type
     */
    protected function createPropertyOfType(string $type)
    {
        // Handles an array of DTOs
        if (strpos($type, '[]') !== false) {
            $type = str_replace('[]', '', $type);

            return PopoFactory::new($type)
                ->random()
                ->make();
        }

        // Handles a DTO
        if (Validator::isDTO($type)) {
            return PopoFactory::new($type)
                ->make();
        }

        switch ($type) {
            case 'array':
                return FakerMap::faker()->words();

            case 'bool':
                return FakerMap::faker()->boolean();

            case 'int':
                return FakerMap::faker()->randomDigit();

            case 'float':
                return FakerMap::faker()->randomFloat();
        }

        return FakerMap::faker()->word();
    }

    protected function createPropertyOfRandomType()
    {
        $type = array_rand($types = [
            'array',
            'bool',
            'int',
            'float',
            'string',
        ], 1);

        return $this->createPropertyOfType($types[$type]);
    }
}
