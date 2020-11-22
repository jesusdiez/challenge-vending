<?php
declare(strict_types=1);

namespace Vending\Domain;

final class Machine
{
    private Inventory $inventory;
    private CoinHolder $coinHolder;
    private Changer $changer;

    public function __construct(Inventory $inventory, CoinHolder $coinHolder)
    {
        $this->inventory = $inventory;
        $this->coinHolder = $coinHolder;
        $this->changer = new Changer();
    }

    public function get(ItemSelector $itemSelector): array
    {
        if (!$this->inventory->hasStock($itemSelector)) {
            throw new \RuntimeException('No Stock!');
        }

        $item = $this->inventory->get($itemSelector);
        $totalInserted = $this->coinHolder->total();
        if ($item->price()->greaterThan($totalInserted)) {
            throw new \RuntimeException('Not enough money!');
        }

        $pendingChange = $totalInserted->substract($item->price());
        if (!$this->changer->hasChange($pendingChange)) {
            throw new \RuntimeException('Unable to provide change, insert exact change!');
        }

        $this->inventory->sell($itemSelector);

        return $this->changer->change($pendingChange);
    }

    public function insert(Coin $coin): void
    {
        $this->coinHolder->add($coin);
    }

    public function returnCoin(): array
    {
        return $this->coinHolder->flush();
    }

    public function service(array $change, array $inventory): bool
    {
        return true;
    }
}
