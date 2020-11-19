<?php
declare(strict_types=1);

namespace Vending\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Vending\Coin;

class CoinTest extends TestCase
{
    /** @dataProvider providerInstantes */
    public function testInstantiatesFromString($value): void
    {
        self::assertInstanceOf(Coin::class, Coin::fromString($value));
    }

    public function providerInstantes(): array
    {
        return [
            ['1'],
            ['1.00'],
            ['0.25'],
            ['0.10'],
            ['0.05'],
        ];
    }

    /** @dataProvider providerFailsInstantiation */
    public function testFailsInstantiationFromStringForWrongValues($value): void
    {
        self::expectException(InvalidArgumentException::class);
        self::assertInstanceOf(Coin::class, Coin::fromString($value));
    }

    public function providerFailsInstantiation(): array
    {
        return [
            ['3.00'],
            ['0'],
            ['-1'],
            ['chorizo'],
        ];
    }

    /** @dataProvider providerValue */
    public function testValue($centsValue, $input): void
    {
        self::assertEquals($centsValue, Coin::fromString($input)->value());
    }

    public function providerValue(): array
    {
        return [
            [100, '1.00'],
            [25, '0.25'],
            [10, '0.10'],
        ];
    }

    /** @dataProvider providerToString */
    public function testToString($input): void
    {
        self::assertEquals($input, (string) Coin::fromString($input));
    }

    public function providerToString(): array
    {
        return [
            ['1.00'],
            ['0.25'],
            ['0.10'],
        ];
    }

}
