<?php

namespace Tests\Unit;

use Elverion\PhpBc\Math\BcNumber;
use PHPUnit\Framework\TestCase;

class NumberTest extends TestCase
{
    public static function instantiables(): array
    {
        return [
            [0, "0"],
            [1000, "1000"],
            ["0", "0"],
            ["1234", "1234"],
            [new BcNumber(0), "0"]
        ];
    }

    /** @dataProvider instantiables */
    public function test_can_instantiate_from(mixed $input, $expected)
    {
        $number = new BcNumber($input);
        self::assertSame($expected, (string) $number);
    }

    public static function addables(): array
    {
        return [
            [0, 1, "1.000000"],
            [-1, 1, "0.000000"],
        ];
    }

    /** @dataProvider addables */
    public function test_can_add(mixed $left, mixed $right, string $expectated)
    {
        $result = (new BcNumber($left))->add($right);

        self::assertSame($expectated, (string) $result);
    }

    public static function subtractables(): array
    {
        return [
            [0, 1, "-1.000000"],
            [-1, 1, "-2.000000"],
        ];
    }

    /** @dataProvider subtractables */
    public function test_can_subtract(mixed $left, mixed $right, string $expectated)
    {
        $result = (new BcNumber($left))->sub($right);

        self::assertSame($expectated, (string) $result);
    }

    public static function multipliables(): array
    {
        return [
            [0, 1, "0.000000"],
            [-1, 5, "-5.000000"],
            [2, 500, "1000.000000"]
        ];
    }

    /** @dataProvider multipliables */
    public function test_can_multiply(mixed $left, mixed $right, string $expectated)
    {
        $result = (new BcNumber($left))->mul($right);

        self::assertSame($expectated, (string) $result);
    }

    public static function dividables(): array
    {
        return [
            [0, 1, "0.000000"],
            [1, 5, "0.200000"],
            [100, 2, "50.000000"]
        ];
    }

    /** @dataProvider dividables */
    public function test_can_divide(mixed $left, mixed $right, string $expectated)
    {
        $result = (new BcNumber($left))->div($right);

        self::assertSame($expectated, (string) $result);
    }
}