<?php
declare(strict_types=1);

namespace Vending\Domain;

class Machine
{
    private Inventory $inventory;
    private CoinHolder $liveCoins;
    private CoinHolder $storedCoins;

    public function __construct()
    {
        $this->inventory = new Inventory();
        $this->storedCoins = new CoinHolder();
        $this->liveCoins = new CoinHolder();
    }

    public function get(ItemSelector $itemSelector): array
    {
        if (!$this->inventory->hasStock($itemSelector)) {
            throw new \RuntimeException('No Stock!');
        }

        $item = $this->inventory->get($itemSelector);
        $totalInserted = $this->liveCoins->total();
        if ($item->price()->greaterThan($totalInserted)) {
            throw new \RuntimeException('Not enough money!');
        }

        $change = [];
        $pendingChange = $totalInserted->substract($item->price());
        if ($pendingChange->greaterThan(Money::fromInt(0))) {
            $changer = new Changer($this->allAvailableCoins());
            $change = $changer->change($pendingChange);
            if ($change === null) {
                throw new \RuntimeException('Unable to provide change, insert exact change!');
            }
        }

        $this->inventory->sell($itemSelector);
        $this->storedCoins->addArray($this->liveCoins->flush());
        $this->storedCoins->retrieveArray($change);

        return array_merge([$itemSelector], $change);
    }

    public function insert(Coin $coin): void
    {
        $this->liveCoins->add($coin);
    }

    public function returnCoins(): array
    {
        return $this->liveCoins->flush();
    }

    public function serviceSetChange(Coin $coin, int $count): void
    {
        $this->storedCoins->set($coin, $count);
    }

    public function serviceSetItems(Item $item): bool
    {
        $this->inventory->set($item);
    }

    private function allAvailableCoins(): CoinHolder
    {
        $coins = new InMemoryCoinHolder();
        $coins->addArray($this->liveCoins->getAll());
        $coins->addArray($this->storedCoins->getAll());

        return $coins;
    }
}
