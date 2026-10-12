<?php

declare(strict_types=1);

namespace VicidialApi\Parameters;

use ReflectionClass;
use ReflectionProperty;

/**
 * Base class for typed request parameter objects.
 *
 * Public readonly properties are converted to the snake_case query parameters
 * VICIdial expects. `null` properties are omitted, booleans are rendered with the
 * class's YES / NO constants, and anything VICIdial accepts that is not modelled
 * as a property can be added with `with()`.
 */
abstract class Parameters
{
    /** Rendering of `true` / `false` booleans. */
    protected const YES = 'Y';

    protected const NO = 'N';

    /**
     * Property name => query parameter name, for the few that do not follow snake_case.
     *
     * @var array<string, string>
     */
    protected const KEY_MAP = [];

    /** @var array<string, string> */
    private array $extra = [];

    /**
     * Add a parameter that is not modelled as a property (custom list fields, new VICIdial options).
     */
    public function with(string $name, string|int|float|bool $value): static
    {
        $clone = clone $this;
        $clone->extra[$name] = $this->render($value);

        return $clone;
    }

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        $parameters = [];

        foreach ((new ReflectionClass($this))->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            /** @var string|int|float|bool|null $value */
            $value = $property->getValue($this);

            if ($value === null) {
                continue;
            }

            $parameters[$this->key($property->getName())] = $this->render($value);
        }

        return $parameters + $this->extra;
    }

    private function key(string $property): string
    {
        return static::KEY_MAP[$property] ?? strtolower((string) preg_replace('/(?<=[a-z0-9])([A-Z])/', '_$1', $property));
    }

    private function render(string|int|float|bool $value): string
    {
        if (is_bool($value)) {
            return $value ? static::YES : static::NO;
        }

        return (string) $value;
    }
}
