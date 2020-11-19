<?php
declare(strict_types=1);

namespace Lib\Tests;

use InvalidArgumentException;
use Lib\Enum;
use PHPUnit\Framework\TestCase;

class EnumTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testInstantiationFromString(): void
    {
        self::assertInstanceOf(TestableEnum::class, TestableEnum::fromString('V2'));
    }

    public function testDynamicStaticInstantiation(): void
    {
        self::assertInstanceOf(TestableEnum::class, TestableEnum::ONE());
    }

    public function testDynamicStaticInstantiationFailsOnInvalidValue(): void
    {
        self::expectException(InvalidArgumentException::class);
        TestableEnum::SIXTYNINE();
    }

    public function testValues(): void
    {
        self::assertEquals(['ONE' => 'V1', 'TWO' => 'V2', 'THREE' => 3], TestableEnum::values());
    }

    public function testEquals(): void
    {
        self::assertTrue(TestableEnum::ONE()->equals(TestableEnum::ONE()));
    }

    public function testNotEquals(): void
    {
        self::assertFalse(TestableEnum::ONE()->equals(TestableEnum::TWO()));
    }

    public function testValue(): void
    {
        self::assertEquals(3, TestableEnum::THREE()->value());
    }

    public function testToString(): void
    {
        self::assertEquals('3', (string) TestableEnum::THREE());
    }
}

class TestableEnum extends Enum {
    const ONE = 'V1';
    const TWO = 'V2';
    const THREE = 3;
}

