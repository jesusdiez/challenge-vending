<?php
declare(strict_types=1);

namespace Vending;

use PHPUnit\Framework\TestCase;

class InventoryTest extends TestCase
{
    private Inventory $sut;

    protected function setUp(): void
    {
        $this->sut = new Inventory();
    }

    public function testSetStockOnCreation(): void
    {
        $item = ItemSelector::WATER();
        $item2 = ItemSelector::JUICE();
        $this->sut = new Inventory(
            [
                $item->value() => 1,
                $item2->value() => 0,
            ]
        );
        self::assertTrue($this->sut->has($item));
        self::assertFalse($this->sut->has($item2));
    }

    public function testSellWithStock(): void
    {
        $item = ItemSelector::SODA();
        $this->sut->addStock($item);

        self::assertTrue($this->sut->has($item));
        $this->sut->sell($item);
        self::assertFalse($this->sut->has($item));
    }

    public function testFailsSellWithNotock(): void
    {
        self::expectException(\RuntimeException::class);
        $item = ItemSelector::SODA();
        $this->sut->sell($item);
    }

    public function testPriceControl(): void
    {
        $item = ItemSelector::WATER();
        self::assertInstanceOf(Money::class, $this->sut->price($item));
    }
}
