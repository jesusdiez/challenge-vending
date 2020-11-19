<?php
declare(strict_types=1);

namespace Vending;

final class Machine
{
    private array $coinBuffer = [];
    private array $productPrices = [
        'SODA' => 150,
        'JUICE' => 100,
        'WATER' => 65,
    ];
    private array $availableItems = [
        'SODA' => 1,
        'JUICE' => 1,
        'WATER' => 1,
    ];
    private array $availableChange = [
        Coin::UNIT => 0,
        Coin::CENT25 => 0,
        Coin::CENT10 => 0,
        Coin::CENT5 => 0,
    ];

    public function get(string $product): array
    {
        $moneyInserted = \array_sum($this->coinBuffer);
        $pendingChange = $moneyInserted - $this->productPrices[$product];
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
                    array_push($response, $coin);
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
        $response = $this->coinBuffer;
        $this->coinBuffer = [];

        return $response;
    }

    public function service(array $change, array $inventory): bool
    {
        return true;
    }
}