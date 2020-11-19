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
        $item = Item::WATER();
        $item2 = Item::JUICE();
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
        $item = Item::SODA();
        $this->sut->addStock($item);

        self::assertTrue($this->sut->has($item));
        $this->sut->sell($item);
        self::assertFalse($this->sut->has($item));
    }

    public function testFailsSellWithNotock(): void
    {
        self::expectException(\RuntimeException::class);
        $item = Item::SODA();
        $this->sut->sell($item);
    }

    public function testPriceControl(): void
    {
        $item = Item::WATER();
        self::assertIsInt($this->sut->price($item));
    }
}
