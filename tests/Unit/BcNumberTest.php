<?php

namespace Tests\Unit;

use Elverion\PhpBc\Math\BcNumber;
use PHPUnit\Framework\TestCase;

class BcNumberTest extends TestCase
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
    public function testCanInstantiateFrom(mixed $input, $expected)
    {
        $number = new BcNumber($input);
        self::assertSame($expected, (string) $number);
    }

    public function testCanSetPrecision()
    {
        $num = new BcNumber(1.123456789, 9);
        self::assertSame((string) $num, "1.123456789");
    }

    public static function comparisons(): array
    {
        return [
            ['10', "10.000000"],
            [0.1 + 0.2, "0.3000000"], // Would be false if floats
        ];
    }

    /** @dataProvider comparisons */
    public function testCanCompare($left, $right)
    {
        $result = (new BcNumber($left))->equals($right);
        self::assertTrue($result);
    }

    public static function addables(): array
    {
        return [
            ['0', '1', "1.000000"],
            ['-1', '1', "0.000000"],
        ];
    }

    /** @dataProvider addables */
    public function testCanAdd(mixed $left, mixed $right, string $expectated)
    {
        $result = (new BcNumber($left))->add($right);

        self::assertSame($expectated, (string) $result);
    }

    public static function subtractables(): array
    {
        return [
            ['0', '1', "-1.000000"],
            ['-1', '1', "-2.000000"],
            ['50.03', '45.42', "4.610000"], // In floats, would be 4.609999999999999 -- an error
        ];
    }

    /** @dataProvider subtractables */
    public function testCanSubtract(mixed $left, mixed $right, string $expectated)
    {
        $result = (new BcNumber($left))->sub($right);

        self::assertSame($expectated, (string) $result);
    }

    public static function multipliables(): array
    {
        return [
            ['0', '1', "0.000000"],
            ['-1', '5', "-5.000000"],
            ['2', '500', "1000.000000"],
        ];
    }

    /** @dataProvider multipliables */
    public function testCanMultiply(mixed $left, mixed $right, string $expectated)
    {
        $result = (new BcNumber($left))->mul($right);

        self::assertSame($expectated, (string) $result);
    }

    public static function dividables(): array
    {
        return [
            ['0', '1', "0.000000"],
            ['1', '5', "0.200000"],
            ['100', '2', "50.000000"]
        ];
    }

    /** @dataProvider dividables */
    public function testCanDivide(mixed $left, mixed $right, string $expectated)
    {
        $result = (new BcNumber($left))->div($right);

        self::assertSame($expectated, (string) $result);
    }

    public static function powers(): array
    {
        return [
            ['1', '10', "1.000000"],
            ['3', '3', "27.000000"],
            ['1.2345', '6', "3.539537"],
        ];
    }

    /** @dataProvider powers */
    public function testCanPower(mixed $num, mixed $exponent, string $expectated)
    {
        $result = (new BcNumber($num))->pow($exponent);

        self::assertSame($expectated, (string) $result);
    }

    public static function squares(): array
    {
        return [
            ['1', "1.000000"],
            ['2', "1.414213"],
            ['198236', "445.237015"],
        ];
    }

    /** @dataProvider squares */
    public function testCanSquareRoot(mixed $input, string $expectated)
    {
        $result = (new BcNumber($input))->sqrt();

        self::assertSame($expectated, (string) $result);
    }

    public static function modulos(): array
    {
        return [
            ['10', '3', "1.000000"]
        ];
    }

    /** @dataProvider modulos */
    public function testCanModulo(mixed $left, mixed $right, string $expectated)
    {
        $result = (new BcNumber($left))->mod($right);
        self::assertSame($expectated, (string) $result);
    }

    public function testCanRound()
    {
        self::assertSame("1.23", (new BcNumber(1.2345))->round()); // round down
        self::assertSame("1234.56", (new BcNumber(1234.5555))->round()); // round up
        self::assertSame("1.2345", (new BcNumber(1.23450))->round(4)); // 4 places - no trailing
        self::assertSame("1.200000", (new BcNumber(1.2))->round(6)); // 6 places - trailing 0s
    }

    public function testCanFloor()
    {
        self::assertSame("1.23", (new BcNumber(1.2345))->floor());
        self::assertSame("1234.55", (new BcNumber(1234.5555))->floor());
        self::assertSame("1.2345", (new BcNumber(1.23456))->floor(4));
    }

    public function testCanCeil()
    {
        self::assertSame("1.24", (new BcNumber(1.2345))->ceil());
        self::assertSame("1234.00", (new BcNumber(1234.0))->ceil());
        self::assertSame("1234.001", (new BcNumber(1234.0001))->ceil(3));
        self::assertSame("1.2346", (new BcNumber(1.23456))->ceil(4));
    }

    public function testChains()
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