<?php

namespace VicidialApi\ValueObjects;

use VicidialApi\Contracts\StringableContract;

class Parameter implements StringableContract
{
    public function __construct(
        private readonly mixed $value,
    ) {
        // ..
    }

    public static function from(mixed $value): self
    {
        return new self($value);
    }

    /**
     * @param  array<string|int>  $values
     */
    public static function fromArray(array $values): self
    {
        return new self(implode(',', $values));
    }

    public static function fromObject(object $value): self
    {
        return new self(json_encode($value) ?: '');
    }

    public function toString(): string
    {
        if (is_scalar($this->value) || $this->value === null) {
            return strval($this->value);
        }

        if (is_object($this->value)) {
            return $this->fromObject($this->value)->toString();
        }

        if (is_array($this->value)) {
            return $this->fromArray($this->value)->toString();
        }

        throw new \InvalidArgumentException('Parameter value must be scalar or null');
    }
}
