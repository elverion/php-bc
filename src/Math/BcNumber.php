<?php

namespace Elverion\PhpBc\Math;

class BcNumber
{
    protected string $value;
    protected int $precision = 6;

    public function __construct(self|float|string $from = 0, ?int $precision = null)
    {
        $this->value = $this->convertFrom($from);
        $this->precision = $precision !== null ? $precision : $this->precision;
    }

    protected static function convertFrom(mixed $value): string
    {
        if (is_a($value, static::class)) {
            return (string) $value;
        }

        if (is_numeric($value)) {
            return (string) $value;
        }

        throw new \DomainException("Invalid type for number conversion");
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function toCurrency($precision = 2): string
    {
        //return number_format($this->toFloat(), $precision);
    }

    protected function doOperation(string $op, string $left, string $right): static
    {
        $result = $op($left, $right, $this->precision);
        return new static($result);
    }


    public function add(self|float|string $value): static
    {
        return $this->doOperation('bcadd', $this, $this->convertFrom($value));
    }

    public function sub(self|float|string $value): static
    {
        return $this->doOperation('bcsub', $this, $this->convertFrom($value));
    }

    public function mul(self|float|string $value): static
    {
        return $this->doOperation('bcmul', $this, $this->convertFrom($value));
    }

    public function div(self|float|string $value): static
    {
        return $this->doOperation('bcdiv', $this, $this->convertFrom($value));
    }

    public function mod(self|float|string $value): static
    {
        return $this->doOperation('bcmod', $this, $this->convertFrom($value));
    }

    public function pow(self|float|string $value): static
    {
        return $this->doOperation('bcpow', $this, $this->convertFrom($value));
    }

    public function sqrt(self|float|string $value): static
    {
        return $this->doOperation('bcsqrt', $this, $this->convertFrom($value));
    }
}