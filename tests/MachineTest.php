<?php
declare(strict_types=1);

namespace Vending\Tests;

use RuntimeException;
use Vending\Coin;
use Vending\Inventory;
use Vending\Item;
use Vending\Machine;
use PHPUnit\Framework\TestCase;

class MachineTest extends TestCase
{
    private Machine $sut;

    protected function setUp(): void
    {
        $inventoryItems = array_combine(Item::values(), array_fill(0, count(Item::values()), 1));
        $this->sut = new Machine(new Inventory($inventoryItems));
    }

    public function testBuySodaWithExactChange(): void
    {
        $this->sut->insert(Coin::fromString('1'));
        $this->sut->insert(Coin::fromString('0.25'));
        $this->sut->insert(Coin::fromString('0.25'));
        self::assertEquals(['SODA'], $this->sut->get(Item::SODA()));
    }

    public function testStartAddingMoneyButReturnCoins(): void
    {
        $this->sut->insert(Coin::fromString('0.10'));
        $this->sut->insert(Coin::fromString('0.10'));

        self::assertEquals([0.10, 0.10], $this->sut->returnCoin());
    }

    public function testBuyWaterWithChange(): void
    {
        $this->sut->insert(Coin::fromString('1'));

        self::assertEquals(['WATER', '0.25', '0.10'], $this->sut->get(Item::WATER()));
    }

    public function testUnableToSellWhenNoItemStock(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('No Stock!');
        $this->sut = new Machine(new Inventory());
        $this->sut->insert(Coin::UNIT());
        $this->sut->get(Item::WATER());
    }

    public function testService(): void
    {
        $change = [0.05 => 50, 0.1 => 50, 0.25 => 50, 1 => 25];
        $inventory = [
            'SODA' => 20,
            'JUICE' => 20,
            'WATER' => 20,
        ];
        self::assertTrue($this->sut->service($change, $inventory));
    }
}
