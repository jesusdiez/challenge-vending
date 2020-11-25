<?php
declare(strict_types=1);

namespace Vending\Tests\Domain;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Vending\Domain\Item;
use Vending\Domain\ItemSelector;
use Vending\Domain\Money;
use Vending\Infrastructure\Inventory;

class InMemoryInventoryTest extends TestCase
{
    private Inventory $sut;

    protected function setUp(): void
    {
        $this->sut = new Inventory();
    }

    public function testAddsItemsToInventoryOnCreation(): void
    {
        $itemSel = ItemSelector::WATER();
        $itemSel2 = ItemSelector::JUICE();
        $this->sut = new Inventory(
            [
                $itemSel->value() => 1,
                $itemSel2->value() => 0,
            ]
        );
        self::assertTrue($this->sut->hasStock($itemSel));
        self::assertFalse($this->sut->hasStock($itemSel2));
    }

    public function testHasChecksItemExistence(): void
    {
        $itemSel = ItemSelector::SODA();
        $this->sut->create(new Item($itemSel, Money::fromInt(100), 1));

        self::assertTrue($this->sut->hasStock($itemSel));
        self::assertFalse($this->sut->hasStock(ItemSelector::WATER()));
    }

    public function testSellWorks(): void
    {
        $itemSel = ItemSelector::SODA();
        $this->sut->create(new Item($itemSel, Money::fromInt(100), 1));
        $this->sut->sell($itemSel);
        self::assertFalse($this->sut->hasStock($itemSel));
    }

    public function testSellFailsWhenTheItemDoesNotExist(): void
    {
        $itemSel = ItemSelector::SODA();

        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('No stock');
        $this->sut->sell($itemSel);
    }

    public function testSellFailsWhenTheItemDoesNotHaveExistences(): void
    {
        $itemSel = ItemSelector::SODA();
        $this->sut->create(new Item($itemSel, Money::fromInt(100), 0));

        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('No stock');
        $this->sut->sell($itemSel);
    }
}
