<?php
declare(strict_types=1);

use Vending\Machine;
use PHPUnit\Framework\TestCase;

class MachineTest extends TestCase
{
    public function testBuySodaWithExactChange(): void
    {
        $machine = new Machine();
        $machine->insert(1);
        $machine->insert(0.25);
        $machine->insert(0.25);
        self::assertEquals(['SODA'], $machine->get('SODA'));
    }

    public function testStartAddingMoneyButReturnCoins(): void
    {
        $machine = new Machine();
        $machine->insert(0.10);
        $machine->insert(0.10);

        self::assertEquals([0.10, 0.10], $machine->returnCoin());
    }

    public function testBuyWaterWithouthExactChange(): void
    {
        $machine = new Machine();
        $machine->insert(1);

        self::assertEquals(['WATER', 0.25, 0.10], $machine->get('WATER'));
    }
}
