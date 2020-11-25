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
        $this->storedCoins = CoinHolder::createEmpty();
        $this->liveCoins = CoinHolder::createEmpty();
    }

    public function get(ItemSelector $itemSelector): array
    {
        if (!$this->inventory->hasStock($itemSelector)) {
            throw new \RuntimeException('No item Stock!');
        }

        $item = $this->inventory->get($itemSelector);
        $totalInserted = $this->liveCoins->total();
        if ($item->price()->greaterThan($totalInserted)) {
            throw new \RuntimeException('Not enough money to buy your item!');
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

        return $change;
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

    public function serviceSetItem(ItemSelector $itemSelector, Money $price, int $count): void
    {
        $this->inventory->set(new Item($itemSelector, $price, $count));
    }

    private function allAvailableCoins(): CoinHolder
    {
        $coins = CoinHolder::createEmpty();
        $coins->addArray($this->liveCoins->getAll());
        $coins->addArray($this->storedCoins->getAll());

        return $coins;
    }
}
