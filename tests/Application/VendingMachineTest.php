<?php
declare(strict_types=1);

namespace Vending\Tests\Application;

use PHPUnit\Framework\MockObject\MockObject;
use Prophecy\PhpUnit\ProphecyTrait;
use Vending\Application\VendingMachine;
use PHPUnit\Framework\TestCase;
use Vending\Domain\Coin;
use Vending\Domain\ItemSelector;
use Vending\Domain\Machine;
use Vending\Infrastructure\Persistence\MemcachedMachineRepository;

class VendingMachineTest extends TestCase
{
    use ProphecyTrait;

    /** @var MockObject|MemcachedMachineRepository */
    private $repo;
    /** @var MockObject|Machine */
    private $machine;

    protected function setUp(): void
    {
        parent::setUp();

        $this->machine = $this->prophesize(Machine::class);
        $this->repo = $this->prophesize(MemcachedMachineRepository::class);
        $this->repo->get()
            ->willReturn($this->machine->reveal());

        $this->sut = new VendingMachine($this->repo->reveal());
    }

    public function testInsertCoin()
    {
        $coin = Coin::CENT5();
        $this->machine->insert($coin)
            ->shouldBeCalled();
        $this->repo->persist($this->machine)
            ->shouldBeCalled();

        $this->sut->insertCoin($coin);
    }

    public function testReturnCoins()
    {
        $this->machine->returnCoins()
            ->willReturn([]);
        $this->machine->returnCoins()
            ->shouldBeCalled();
        $this->repo->persist($this->machine)
            ->shouldBeCalled();

        $this->sut->returnCoins();
    }

    public function testGetItem()
    {
        $itemSelector = ItemSelector::JUICE();

        $this->machine->get(ItemSelector::JUICE())
            ->willReturn([])
            ->shouldBeCalled();
        $this->repo->persist($this->machine)
            ->shouldBeCalled();

        $this->sut->getItem($itemSelector);
    }
}
