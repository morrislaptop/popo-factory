<?php

namespace Morrislaptop\PopoFactory;

use Morrislaptop\PopoFactory\Normalizer\CarbonDenormalizer;
use ReflectionClass;
use ReflectionProperty;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @psalm-consistent-constructor
 */
class PopoFactory
{
    protected ?int $count = null;

    /**
     * @var class-string
     */
    protected string $dataTransferObjectClass;

    protected array $states = [];

    protected Serializer $serializer;

    /**
     * @param class-string $dataTransferObjectClass
     */
    public static function new(string $dataTransferObjectClass): static
    {
        return new static($dataTransferObjectClass);
    }

    /**
     * @param class-string $dataTransferObjectClass
     */
    public function __construct(string $dataTransferObjectClass, Serializer $serializer = null)
    {
        $this->dataTransferObjectClass = $dataTransferObjectClass;
        $this->serializer = $serializer ?: new Serializer([
            new CarbonDenormalizer,
            new DateTimeNormalizer,
            new ArrayDenormalizer,
            new ObjectNormalizer,
        ], []);
    }

    /***************************************************************************
     * Factory Options
     **************************************************************************/

    /**
     * Sets the number of Data Transfer Objects we should generate.
     *
     * @return static
     */
    public function count(int $count): static
    {
        $clone = clone $this;

        $clone->count = $count;

        return $clone;
    }

    /**
     * Sets a random number of Data Transfer Objects we should generate.
     *
     * @return static
     */
    public function random(int $min = 3, int $max = 100): static
    {
        return $this->count(random_int($min, $max));
    }

    /**
     * Create a sequence of overrides.
     *
     * @return static
     */
    public function sequence(array ...$sequence): static
    {
        return $this->state(Sequence::make(...$sequence));
    }

    /**
     * Manually override attributes by passing an array of values.
     *
     * @param callable|array $state
     *
     * @return static
     */
    public function state($state): static
    {
        $clone = clone $this;

        if (! is_callable($state)) {
            $state = fn (): array => $state;
        }

        $clone->states[] = $state;

        return $clone;
    }

    /***************************************************************************
     * DTO Creator
     **************************************************************************/

    public function make(array $attributes = []): array | object
    {
        // Pass attributes along as state
        if (! empty($attributes)) {
            return $this->state($attributes)->make();
        }

        if (! $this->count) {
            return $this->makeDTO();
        }

        $multipleDTOs = $this->makeDTOs(
            $this->count ?: random_int(3, 100)
        );

        return $multipleDTOs;
    }

    protected function makeDTO(): object
    {
        $class = new ReflectionClass($this->dataTransferObjectClass);
        $parameters = [];
        $properties = $class->getProperties(ReflectionProperty::IS_PUBLIC);

        foreach ($properties as $property) {
            if ($property->isStatic()) {
                continue;
            }

            $propertyName = $property->getName();

            $parameters[$propertyName] = PropertyFactory::new()->make($property);
        }

        foreach ($this->states as $state) {
            $result = $state($parameters);
            $parameters = array_merge($parameters, is_array($result) ? $result : []);
        }

        return $this->serializer->denormalize($parameters, $this->dataTransferObjectClass);
    }

    protected function makeDTOs(int $count): array
    {
        $numberOfDtosCreated = 0;
        $dtos = [];

        while ($numberOfDtosCreated < $count) {
            $dtos[] = $this->makeDTO();
            $numberOfDtosCreated++;
        }

        return $dtos;
    }
}
