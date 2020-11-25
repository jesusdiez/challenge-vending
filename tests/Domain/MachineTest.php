<?php
declare(strict_types=1);

namespace Vending\Tests\Domain;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Vending\Domain\Coin;
use Vending\Domain\ItemSelector;
use Vending\Domain\Machine;
use Vending\Domain\Money;

use function array_walk;

class MachineTest extends TestCase
{
    private Machine $sut;

    protected function setUp(): void
    {
        $this->sut = new Machine();
//        $this->initInventory();
//        $this->initCoinHolder();
    }

    public function testBuySodaWithExactChange(): void
    {
        $this->sut->serviceSetItem(ItemSelector::SODA(), Money::fromString('1.50'), 1);
        $this->sut->insert(Coin::UNIT());
        $this->sut->insert(Coin::CENT25());
        $this->sut->insert(Coin::CENT25());
        self::assertEquals([], $this->sut->get(ItemSelector::SODA()));
    }

    public function testStartAddingMoneyButReturnCoins(): void
    {
        $this->sut->insert(Coin::CENT10());
        $this->sut->insert(Coin::CENT10());

        self::assertEquals([Coin::CENT10(), Coin::CENT10()], $this->sut->returnCoins());
    }

    public function testBuyWaterWithChange(): void
    {
        $this->sut->serviceSetItem(ItemSelector::WATER(), Money::fromString('0.65'), 1);
        $this->setOneCoinOfEach($this->sut);
        $this->sut->insert(Coin::UNIT());

        self::assertEquals(
            [Coin::CENT25(), Coin::CENT10()],
            $this->sut->get(ItemSelector::WATER())
        );
    }

    public function testUnableToBuyWhenNotEnoughMoney(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Not enough money to buy your item!');
        $this->sut->serviceSetItem(ItemSelector::SODA(), Money::fromString('1.50'), 1);
        $this->sut->insert(Coin::UNIT());
        $this->sut->get(ItemSelector::SODA());
    }

    public function testUnableToSellWhenNoItemStock(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('No item Stock!');
        $this->sut->insert(Coin::UNIT());
        $this->sut->get(ItemSelector::WATER());
    }

    public function testUnableToSellWhenNotEnoughChange(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Unable to provide change, insert exact change!');
        $this->sut->serviceSetItem(ItemSelector::SODA(), Money::fromString('1.50'), 1);
        $this->sut->insert(Coin::UNIT());
        $this->sut->insert(Coin::UNIT());
        $this->sut->get(ItemSelector::SODA());
    }

    private function setOneItemOfEach(Machine $machine): void
    {
        $items = ItemSelector::values();
        array_walk(
            $items,
            fn($value) => $machine->serviceSetItem(ItemSelector::fromString($value),)
        );
    }

    private function setOneCoinOfEach(Machine $machine): void
    {
        $coins = Coin::values();
        array_walk(
            $coins,
            fn($coinValue) => $machine->serviceSetChange(Coin::fromInt($coinValue), 1)
        );
    }
}
