<?php
declare(strict_types=1);

namespace Vending\Tests;

use Vending\Money;
use PHPUnit\Framework\TestCase;

class MoneyTest extends TestCase
{
    public function testInstantiates()
    {
        self::assertInstanceOf(Money::class, Money::fromString('1.25'));
    }

    public function testExtractsCents()
    {
        self::assertEquals(125, Money::fromString('1.25')->amountInCents());
    }
    
    public function testToString() 
    {
        self::assertEquals('2.75', (string) Money::fromString('2.75'));
    }
}
