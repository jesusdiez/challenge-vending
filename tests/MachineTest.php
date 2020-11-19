<?php
declare(strict_types=1);

namespace Vending\Tests;

use Vending\Coin;
use Vending\Machine;
use PHPUnit\Framework\TestCase;

class MachineTest extends TestCase
{
    public function testBuySodaWithExactChange(): void
    {
        $machine = new Machine();
        $machine->insert(Coin::fromString('1'));
        $machine->insert(Coin::fromString('0.25'));
        $machine->insert(Coin::fromString('0.25'));
        self::assertEquals(['SODA'], $machine->get('SODA'));
    }

    public function testStartAddingMoneyButReturnCoins(): void
    {
        $machine = new Machine();
        $machine->insert(Coin::fromString('0.10'));
        $machine->insert(Coin::fromString('0.10'));

        self::assertEquals([0.10, 0.10], $machine->returnCoin());
    }

    public function testBuyWaterWithouthExactChange(): void
    {
        $machine = new Machine();
        $machine->insert(Coin::fromString('1'));

        self::assertEquals(['WATER', 0.25, 0.10], $machine->get('WATER'));
    }

    public function testService(): void
    {
        $change = [0.05 => 50, 0.1 => 50, 0.25 => 50, 1 => 25];
        $inventory = [
            'SODA' => 20,
            'JUICE' => 20,
            'WATER' => 20,
        ];
        $machine = new Machine();
        self::assertTrue($machine->service($change, $inventory));
    }
}
