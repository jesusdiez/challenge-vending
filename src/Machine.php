<?php
declare(strict_types=1);

namespace Vending;

final class Machine
{
    private array $coinBuffer = [];
    private array $productPrices = [
        Item::SODA => 150,
        Item::JUICE => 100,
        Item::WATER => 65,
    ];
    private array $availableItems = [
        Item::SODA => 1,
        Item::JUICE => 1,
        Item::WATER => 1,
    ];
    private array $availableChange = [
        Coin::UNIT => 0,
        Coin::CENT25 => 0,
        Coin::CENT10 => 0,
        Coin::CENT5 => 0,
    ];

    public function get(string $product): array
    {
        $totalInserted = \array_reduce($this->coinBuffer, fn($carry, $coin) => $carry + $coin->value(), 0);
        $pendingChange = $totalInserted - $this->productPrices[$product];
        $response = [];
        if ($pendingChange >= 0) {
            $this->availableItems[$product]--;
            array_push($response, $product);
        }
        if ($pendingChange > 0) {
            $usableCoins = Coin::values();
            sort($usableCoins);
            while ($pendingChange >= min($usableCoins)) {
                $coin = max($usableCoins);
                if ($pendingChange >= $coin) {
                    $pendingChange -= $coin;
                    $this->availableChange[$coin]--;
                    array_push($response, (string) $coin/100);
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
