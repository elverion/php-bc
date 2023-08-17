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
            [1_234.567, "1234.567"],
            [new BcNumber(0), "0"],
            [1111.2222229, "1111.2222229"]
        ];
    }

    /** @dataProvider instantiables */
    public function test_can_instantiate_from(mixed $input, $expected)
    {
        $number = new BcNumber($input);
        self::assertSame($expected, (string) $number);
    }

    public static function comparisons(): array
    {
        return [
            [10, "10.000000"],
            [0.1 + 0.2, "0.3000000"], // Would be false if floats
        ];
    }

    /** @dataProvider comparisons */
    public function test_can_compare($left, $right)
    {
        $result = (new BcNumber($left))->equals($right);
        self::assertTrue($result);
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
            [50.03, 45.42, "4.610000"], // In floats, would be 4.609999999999999 -- an error
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
            [2, 500, "1000.000000"],
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

    public static function powers(): array
    {
        return [
            [1, 10, "1.000000"],
            [3, 3, "27.000000"],
            [1.2345, 6, "3.539537"],
        ];
    }

    /** @dataProvider powers */
    public function test_can_power(mixed $num, mixed $exponent, string $expectated)
    {
        $result = (new BcNumber($num))->pow($exponent);

        self::assertSame($expectated, (string) $result);
    }


    public static function squares(): array
    {
        return [
            [1, "1.000000"],
            [2, "1.414213"],
            [198236, "445.237015"],
        ];
    }

    /** @dataProvider squares */
    public function test_can_square_root(mixed $input, string $expectated)
    {
        $result = (new BcNumber($input))->sqrt();

        self::assertSame($expectated, (string) $result);
    }

    public function test_can_round()
    {
        self::assertSame((new BcNumber(1.2345))->round(), "1.23");
        self::assertSame((new BcNumber(1234.5555))->round(), "1234.56");
        self::assertSame((new BcNumber(1.23450))->round(4), "1.2345");
        self::assertSame((new BcNumber(1.2))->round(6), "1.200000");
    }

    public function test_chains()
    {
        // 0.1 + 0.2 == 0.3 -- false if in floats
        self::assertTrue((new BcNumber(0.1))
            ->add(0.2)
            ->equals(0.3));

        // (0.1 + 0.7) * 10 == 8 -- false if in floats
        self::assertTrue((new BcNumber(0.1))
            ->add(0.7)
            ->mul(10)
            ->equals(8));
    }
}