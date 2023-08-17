<?php

namespace Elverion\PhpBc\Math;

class BcNumber
{
    protected string $value;
    protected ?int $precision;

    public function __construct(self|float|string $from = 0, ?int $precision = 6)
    {
        $this->value = $this->convertFrom($from);
        $this->precision = $precision;
    }

    /**
     * Helper function to convert an arbitrary input to a numeric string (if possible)
     * @throws \DomainException
     */
    protected static function convertFrom(mixed $value): string
    {
        if (is_a($value, static::class) || is_numeric($value)) {
            return (string) $value;
        }

        throw new \DomainException("Invalid type `" . gettype($value) . "` for number conversion");
    }

    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * Helper function to call a bcmath operation
     */
    protected function doOperation(string $op, string $left, string $right)
    {
        return $op($left, $right, $this->precision);
    }

    /**
     * Compares the value against a given input. Returns true if they are equal, false if not.
     */
    public function equals(self|float|string $value): bool
    {
        return bccomp((string) $this, $this->convertFrom($value), $this->precision) === 0;
    }

    /**
     * Rounds the value for a given precision point. For example 1.234 rounded with 2 precision = 1.23
     */
    public function round(int $precision = 2)
    {
        return sprintf("%0.{$precision}f", $this->value);
    }

    /**
     * Add an input into current value
     */
    public function add(self|float|string $value): static
    {
        $this->value = $this->doOperation('bcadd', $this, $this->convertFrom($value));
        return $this;
    }

    /**
     * Subtract an input from current value
     */
    public function sub(self|float|string $value): static
    {
        $this->value = $this->doOperation('bcsub', $this, $this->convertFrom($value));
        return $this;
    }

    /**
     * Multiply the current value by an input
     */
    public function mul(self|float|string $value): static
    {
        $this->value = $this->doOperation('bcmul', $this, $this->convertFrom($value));
        return $this;
    }

    /**
     * Alias to mul()
     */
    public function times(self|float|string $value): static
    {
        return $this->mul($value);
    }

    /**
     * Divide current value by an input
     */
    public function div(self|float|string $value): static
    {
        $this->value = $this->doOperation('bcdiv', $this, $this->convertFrom($value));
        return $this;
    }

    /**
     * Get the remainder of a division from current value
     */
    public function mod(self|float|string $value): static
    {
        $this->value = $this->doOperation('bcmod', $this, $this->convertFrom($value));
        return $this;
    }

    /**
     * Raise current value to a power
     */
    public function pow(self|float|string $value): static
    {
        $this->value = $this->doOperation('bcpow', $this, $this->convertFrom($value));
        return $this;
    }

    /**
     * Get the square root of the current value
     */
    public function sqrt(): static
    {
        $this->value = bcsqrt($this, $this->precision);
        return $this;
    }
}