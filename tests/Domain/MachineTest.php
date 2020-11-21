<?php
declare(strict_types=1);

namespace Vending\Tests\Domain;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Vending\Domain\Coin;
use Vending\Domain\ItemSelector;
use Vending\Domain\Machine;
use Vending\Infrastructure\InMemoryInventory;

class MachineTest extends TestCase
{
    private Machine $sut;

    protected function setUp(): void
    {
        $this->sut = new Machine($this->getInventoryWithOneItemOfEach());
    }

    public function testBuySodaWithExactChange(): void
    {
        $this->sut->insert(Coin::fromString('1'));
        $this->sut->insert(Coin::fromString('0.25'));
        $this->sut->insert(Coin::fromString('0.25'));
        self::assertEquals(['SODA'], $this->sut->get(ItemSelector::SODA()));
    }

    public function testStartAddingMoneyButReturnCoins(): void
    {
        $this->sut->insert(Coin::fromString('0.10'));
        $this->sut->insert(Coin::fromString('0.10'));

        self::assertEquals(['0.10', '0.10'], $this->sut->returnCoin());
    }

    public function testBuyWaterWithChange(): void
    {
        $this->sut->insert(Coin::fromString('1'));

        self::assertEquals(['WATER', '0.25', '0.10'], $this->sut->get(ItemSelector::WATER()));
    }

    public function testUnableToSellWhenNoItemStock(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('No Stock!');
        $this->sut = new Machine(new InMemoryInventory());
        $this->sut->insert(Coin::UNIT());
        $this->sut->get(ItemSelector::WATER());
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

    private function getInventoryWithOneItemOfEach()
    {
        $items = array_combine(ItemSelector::values(), array_fill(0, count(ItemSelector::values()), 1));

        return new InMemoryInventory($items);
    }
}
