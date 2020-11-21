<?php
declare(strict_types=1);

namespace Vending\Tests\Domain;

use PHPUnit\Framework\TestCase;
use Vending\Domain\Money;

class MoneyTest extends TestCase
{
    public function testInstantiates(): void
    {
        self::assertInstanceOf(Money::class, Money::fromString('1.25'));
    }

    public function testExtractsCents(): void
    {
        self::assertEquals(125, Money::fromString('1.25')->cents());
    }
    
    public function testToString(): void
    {
        self::assertEquals('2.75', (string) Money::fromString('2.75'));
    }

    public function testAdds(): void
    {
        self::assertEquals(Money::fromInt(6), Money::fromInt(4)->add(Money::fromInt(2)));
    }

    public function testSubstracts(): void
    {
        self::assertEquals(Money::fromInt(12), Money::fromInt(44)->substract(Money::fromInt(32)));
    }

    public function testMultiplies(): void
    {
        self::assertEquals(Money::fromInt(18), Money::fromInt(6)->multiply(3));
    }
}
