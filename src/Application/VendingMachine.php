<?php
declare(strict_types=1);

namespace Vending\Application;

use Vending\Application\Response\GetItemResponse;
use Vending\Application\Response\ReturnCoinResponse;
use Vending\Application\Response\ReturnCoinsResponse;
use Vending\Domain\Coin;
use Vending\Domain\CoinStore;
use Vending\Domain\ItemSelector;
use Vending\Domain\Machine;
use Vending\Domain\MachineRepository;
use Vending\Domain\Money;

final class VendingMachine
{
    private MachineRepository $repo;
    private Machine $machine;

    public function __construct(MachineRepository $repo)
    {
        $this->repo = $repo;
        $this->machine = $this->getMachine();
    }

    public function insertCoin(Coin $coin): void
    {
        $this->machine->insert($coin);
        $this->repo->persist($this->machine);
    }

    public function returnCoins(): ReturnCoinsResponse
    {
        $coins = $this->machine->returnCoins();
        $this->repo->persist($this->machine);

        return new ReturnCoinsResponse($coins);
    }

    public function getItem(ItemSelector $itemSelector): GetItemResponse
    {
        $change = $this->machine->get($itemSelector);
        $this->repo->persist($this->machine);

        return new GetItemResponse($itemSelector, $change);
    }

    public function serviceSetItem(ItemSelector $itemSelector, int $count): void
    {
        $price = $this->itemPrices()[$itemSelector->value()];
        $this->machine->serviceSetItem($itemSelector, $price, $count);
        $this->repo->persist($this->machine);
    }

    public function serviceSetChange(Coin $coin, int $count): void
    {
        $this->machine->serviceSetChange($coin, $count);
        $this->repo->persist($this->machine);
    }

    private function getMachine(): Machine
    {
        return $this->repo->get() ?: $this->initMachine(new Machine());
    }

    private function initMachine(Machine $machine): Machine
    {
        return $this->initCoins($this->initItems($machine));
    }

    private function initItems(Machine $machine): Machine
    {
        $prices = $this->itemPrices();
        $items = \array_keys($prices);
        \array_walk(
            $items,
            fn($item) => $machine->serviceSetItem(ItemSelector::fromString($item), $prices[$item], 1)
        );

        return $machine;
    }

    private function initCoins(Machine $machine): Machine
    {
        $coinValues = Coin::values();
        \array_walk(
            $coinValues,
            fn($coinValue) => $machine->serviceSetChange(
                Coin::fromInt($coinValue),
                10
            )
        );

        return $machine;
    }

    private function itemPrices(): array
    {
        return [
            ItemSelector::JUICE => Money::fromString('1.00'),
            ItemSelector::SODA => Money::fromString('1.50'),
            ItemSelector::WATER => Money::fromString('0.65'),
        ];
    }
}
