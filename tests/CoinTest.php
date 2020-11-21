<?php
declare(strict_types=1);

namespace Vending\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Vending\Coin;
use Vending\Money;

class CoinTest extends TestCase
{
    /** @dataProvider providerInstantes */
    public function testInstantiatesFromString(string $value): void
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
    public function testFailsInstantiationFromStringForWrongValues(string $value): void
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
    public function testValue(int $centsValue, string $input): void
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
    public function testToString(string $input): void
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

    /** @dataProvider providerToString */
    public function testMoneyValue(string $input): void
    {
        self::assertEquals(Money::fromString($input), Coin::fromString($input)->moneyValue());
    }
}
