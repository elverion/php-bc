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
     * Returns the rounded value for a given precision point. For example 1.234 rounded with 2 precision = 1.23
     * Does *NOT* modify the underlying value.
     */
    public function round(int $precision = 2)
    {
        return sprintf("%0.{$precision}f", round($this->value, $precision));
    }

    /**
     * Returns the rounded-down value for a given precision point. For example 1.555 rounded with 2 precision = 1.55
     * Does *NOT* modify the underlying value.
     */
    public function floor(int $precision = 2)
    {
        // Subtract some small piece that would cause round() to round down.
        // The number of zeros must be proportional to our precision.
        // For example, to round-down to 2 decimal places, we'd need to subtract 0.005
        $floorBy = '0.' . str_repeat(0, $precision) . '5';
        $val = bcsub((string) $this->value, $floorBy, $precision + 1);

        return sprintf("%0.{$precision}f", round($val, $precision));
    }

    /**
     * Returns the rounded-up value for a given precision point. For example 1.111 rounded with 2 precision = 1.12
     * Does *NOT* modify the underlying value.
     */
    public function ceil(int $precision = 2)
    {
        // Add some small piece that would cause round() to round up.
        // See notes in floor().
        // Additionally, we suffix with 4 instead of 5 to prevent accidentally over-rounding
        // for numbers like 1.0.
        // ie. precision = 2 : (1.0 + 0.005) = 1.005, rounded to 2 decimal places would result in 1.01, instead of 1.0
        $floorBy = '0.' . str_repeat(0, $precision) . '4';
        $val = bcadd((string) $this->value, $floorBy, $precision + 1);

        return sprintf("%0.{$precision}f", round($val, $precision));
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