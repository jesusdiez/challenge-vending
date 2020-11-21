<?php
declare(strict_types=1);

namespace Vending;

final class Machine
{
    private Inventory $inventory;
    private array $coinBuffer = [];
    private Changer $changer;

    public function __construct(Inventory $inventory)
    {
        $this->inventory = $inventory;
        $this->changer = new Changer();
    }

    public function get(Item $item): array
    {
        if (!$this->inventory->has($item)) {
            throw new \RuntimeException('No Stock!');
        }
        $totalInserted = \array_reduce($this->coinBuffer, fn($carry, $coin) => $carry + $coin->value(), 0);
        $pendingChange = $totalInserted - $this->inventory->price($item);
        $response = [];
        if ($pendingChange >= 0) {
            $this->inventory->sell($item);
            array_push($response, $item->value());
        }
        if ($pendingChange > 0) {
            $response = array_merge(
                $response,
                array_map(fn(Coin $c) => $c->value() / 100, $this->changer->change($pendingChange))
            );
        }

        return $response;
    }

    public function insert(Coin $coin): void
    {
        array_push($this->coinBuffer, $coin);
    }

    public function returnCoin(): array
    {
        $response = array_map(fn($coin) => (string) $coin, $this->coinBuffer);
        $this->coinBuffer = [];

        return $response;
    }

    public function service(array $change, array $inventory): bool
    {
        return true;
    }
}
