<?php

namespace Morrislaptop\PopoFactory;

use ReflectionClass;
use ReflectionProperty;
use Spatie\DataTransferObject\DataTransferObject;
use Morrislaptop\PopoFactory\Exceptions\InvalidObjectException;
use Symfony\Component\Serializer\Serializer;

class PopoFactory
{
    protected int $count;
    protected string $dataTransferObjectClass;
    protected array $states = [];
    protected Serializer $serializer;

    public static function new()
    {
        return new static;
    }

    public function __construct(Serializer $serializer = null)
    {
        $this->serializer = $serializer ?: new Serializer([
            new \Spatie\EventSourcing\Support\CarbonNormalizer,
            // new \Spatie\EventSourcing\Support\ModelIdentifierNormalizer,
            new \Symfony\Component\Serializer\Normalizer\DateTimeNormalizer,
            new \Symfony\Component\Serializer\Normalizer\ArrayDenormalizer,
            new \Spatie\EventSourcing\Support\ObjectNormalizer,
        ], []);
    }

    /***************************************************************************
     * Factory Options
     **************************************************************************/

    /**
     * Sets the number of Data Transfer Objects we should generate.
     */
    public function count(int $count)
    {
        $clone        = clone $this;
        $clone->count = $count;

        return $clone;
    }

    /**
     * Sets a random number of Data Transfer Objects we should generate.
     */
    public function random(int $min = 3, int $max = 100)
    {
        return $this->count(random_int($min, $max));
    }

    /**
     * Sets the Data Transfer Object we are working with.
     */
    public function dto(string $dataTransferObject)
    {
        if (! class_exists($dataTransferObject)) {
            throw new InvalidObjectException(
                "Class $dataTransferObject does not exist!"
            );
        }

        $clone                          = clone $this;
        $clone->dataTransferObjectClass = $dataTransferObject;

        return $clone;
    }

    /**
     * Create a sequence of overrides.
     */
    public function sequence(...$sequence)
    {
        return $this->state(Sequence::make(...$sequence));
    }

    /**
     * Manually override attributes by passing an array of values.
     *
     * @param callable|array $state
     */
    public function state($state)
    {
        $clone = clone $this;

        if (! is_callable($state)) {
            $state = fn () => $state;
        }

        $clone->states[] = $state;

        return $clone;
    }

    /**
     * Sets multiple states.
     */
    public function states(array $states)
    {
        $clone = clone $this;

        foreach ($states as $state) {
            $clone = $this->state($state);
        }

        return $clone;
    }

    /***************************************************************************
     * DTO Creator
     **************************************************************************/

    public function make(array $attributes = [])
    {
        if (! isset($this->dataTransferObjectClass)) {
            throw new InvalidObjectException(
                'Please specify an Object to be generated!'
            );
        }

        // Pass attributes along as state
        if (! empty($attributes)) {
            return $this->state($attributes)->make();
        }

        if (! isset($this->count)) {
            return $this->makeDTO();
        }

        $multipleDTOs = $this->makeDTOs(
            $this->count ?? random_int(3, 100)
        );

        return $multipleDTOs;
    }

    protected function makeDTO()
    {
        $class      = new ReflectionClass($this->dataTransferObjectClass);
        $parameters = [];
        $properties = $class->getProperties(ReflectionProperty::IS_PUBLIC);

        // Resolve all our state options...
        $preset = [];

        foreach ($this->states as $state) {
            $result = $state();
            $preset = array_merge($preset, is_array($result) ? $result : []);
        }

        foreach ($properties as $property) {
            if ($property->isStatic()) {
                continue;
            }

            $propertyName = $property->getName();

            if (isset($preset[$propertyName])) {
                $parameters[$propertyName] = $preset[$propertyName];

                continue;
            }

            $parameters[$propertyName] = PropertyFactory::new()->make($property);
        }

        return $this->serializer->denormalize($parameters, $this->dataTransferObjectClass);
    }

    protected function makeDTOs(int $count): array
    {
        $numberOfDtosCreated = 0;
        $dtos                = [];

        while ($numberOfDtosCreated < $count) {
            $dtos[] = $this->makeDTO();
            $numberOfDtosCreated++;
        }

        return $dtos;
    }
}
