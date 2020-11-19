<?php
declare(strict_types=1);

namespace Vending;

final class Machine
{
    private array $coinBuffer = [];
    private Inventory $inventory;
    private array $availableChange = [
        Coin::UNIT => 0,
        Coin::CENT25 => 0,
        Coin::CENT10 => 0,
        Coin::CENT5 => 0,
    ];

    public function __construct()
    {
        $inventoryItems = array_combine(Item::values(), array_fill(0, count(Item::values()), 1));
        $this->inventory = new Inventory($inventoryItems);
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
            $usableCoins = Coin::values();
            sort($usableCoins);
            while ($pendingChange >= min($usableCoins)) {
                $coin = max($usableCoins);
                if ($pendingChange >= $coin) {
                    $pendingChange -= $coin;
                    $this->availableChange[$coin]--;
                    array_push($response, (string) $coin / 100);
                } else {
                    array_pop($usableCoins);
                }
            }
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
