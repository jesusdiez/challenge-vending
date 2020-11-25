<?php
declare(strict_types=1);

namespace Vending\Tests\Domain;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Vending\Domain\Coin;
use Vending\Domain\CoinHolder;
use Vending\Domain\ItemSelector;
use Vending\Domain\Machine;

class MachineTest extends TestCase
{
    private Machine $sut;

    protected function setUp(): void
    {
        $this->sut = new Machine();
        $this->initInventory();
        $this->initCoinHolder();
    }

    public function testBuySodaWithExactChange(): void
    {
        $this->sut->insert(Coin::UNIT());
        $this->sut->insert(Coin::CENT25());
        $this->sut->insert(Coin::CENT25());
        self::assertEquals([ItemSelector::SODA()], $this->sut->get(ItemSelector::SODA()));
    }

    public function testStartAddingMoneyButReturnCoins(): void
    {
        $this->sut->insert(Coin::CENT10());
        $this->sut->insert(Coin::CENT10());

        self::assertEquals([Coin::CENT10(), Coin::CENT10()], $this->sut->returnCoin());
    }

    public function testBuyWaterWithChange(): void
    {
        $this->sut->insert(Coin::UNIT());

        self::assertEquals(
            [ItemSelector::WATER(), Coin::CENT25(), Coin::CENT10()],
            $this->sut->get(ItemSelector::WATER())
        );
    }

    public function testUnableToBuyWhenNotEnoughMoney(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Not enough money!');
        $this->sut->insert(Coin::UNIT());
        $this->sut->get(ItemSelector::SODA());
    }

    public function testUnableToSellWhenNoItemStock(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('No Stock!');
        $this->sut = new Machine(
            $this->getInventoryEmpty(),
            $this->getCoinHolderEmpty(),
            $this->getCoinHolderWithOneCoinOfEach()
        );
        $this->sut->insert(Coin::UNIT());
        $this->sut->get(ItemSelector::WATER());
    }

    public function testUnableToSellWhenNotEnoughChange(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Unable to provide change, insert exact change!');
        $this->sut->insert(Coin::UNIT());
        $this->sut->insert(Coin::UNIT());
        $this->sut->get(ItemSelector::SODA());
    }

    public function testService(): void
    {
        $change = [
            0.05 => 50,
            0.1 => 50,
            0.25 => 50,
            1 => 25,
        ];
        $inventory = [
            'SODA' => 20,
            'JUICE' => 20,
            'WATER' => 20,
        ];
        self::assertTrue($this->sut->service($change, $inventory));
    }

    private function getInventoryEmpty(): Inventory
    {
        return new Inventory();
    }

    private function getInventoryWithOneItemOfEach(): Inventory
    {
        $items = array_combine(ItemSelector::values(), array_fill(0, count(ItemSelector::values()), 1));

        return new Inventory($items);
    }

    private function getCoinHolderEmpty(): CoinHolder
    {
        return new InMemoryCoinHolder();
    }

    private function getCoinHolderWithOneCoinOfEach(): CoinHolder
    {
        $coinHolder = new InMemoryCoinHolder();
        $coins = Coin::values();
        \array_walk($coins, fn($value) => $coinHolder->add(Coin::fromInt($value)));

        return $coinHolder;
    }

    private function initCoinHolder()
    {

    }
}
