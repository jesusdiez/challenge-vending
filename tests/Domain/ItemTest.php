<?php
declare(strict_types=1);

namespace Vending\Tests\Domain;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Vending\Domain\ItemSelector;

class ItemTest extends TestCase
{
    /** @dataProvider providerInstantes */
    public function testInstantiatesFromString($value): void
    {
        self::assertInstanceOf(ItemSelector::class, ItemSelector::fromString($value));
    }

    public function providerInstantes(): array
    {
        return [
            ['WATER'],
            ['JUICE'],
            ['SODA'],
        ];
    }

    /** @dataProvider providerFailsInstantiation */
    public function testFailsInstantiationFromStringForWrongValues($value): void
    {
        self::expectException(InvalidArgumentException::class);
        self::assertInstanceOf(ItemSelector::class, ItemSelector::fromString($value));
    }

    public function providerFailsInstantiation(): array
    {
        return [
            ['2'],
            ['chorizo'],
            ['Water'],
        ];
    }
}
