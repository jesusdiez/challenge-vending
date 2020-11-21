<?php
declare(strict_types=1);

namespace Vending;

final class Machine
{
    private Inventory $inventory;
    private CoinPurse $coinPurse;
    private Changer $changer;

    public function __construct(Inventory $inventory)
    {
        $this->inventory = $inventory;
        $this->coinPurse = new CoinPurse();
        $this->changer = new Changer();
    }

    public function get(ItemSelector $itemSelector): array
    {
        if (!$this->inventory->hasStock($itemSelector)) {
            throw new \RuntimeException('No Stock!');
        }
        $item = $this->inventory->get($itemSelector);

        $totalInserted = $this->coinPurse->total();
        $pendingChange = $totalInserted->substract($item->price());
        $response = [];
        if ($pendingChange->cents() >= 0) {
            $this->inventory->sell($itemSelector);
            array_push($response, $itemSelector->value());
        }
        if ($pendingChange->cents() > 0) {
            $response = array_merge(
                $response,
                array_map(fn(Coin $c) => (string) $c, $this->changer->change($pendingChange))
            );
        }

        return $response;
    }

    public function insert(Coin $coin): void
    {
        $this->coinPurse->addCoin($coin);
    }

    public function returnCoin(): array
    {
        return $this->coinPurse->flush();
    }

    public function service(array $change, array $inventory): bool
    {
        return true;
    }
}
