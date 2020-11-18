<?php
declare(strict_types=1);

namespace Vending;

final class Machine
{
    private static array $coins = [1, 0.25, 0.10, 0.05];
    private array $coinBuffer = [];
    private array $productPrices = [
        'SODA' => 1.5,
        'JUICE' => 1.0,
        'WATER' => 0.65,
    ];
    private array $availableItems = [
        'SODA' => 1,
        'JUICE' => 1,
        'WATER' => 1,
    ];
    private array $availableChange = [
        1 => 0,
        0.25 => 0,
        0.10 => 0,
        0.05 => 0
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
            $usableCoins = self::$coins;
            sort($usableCoins);
            while ($pendingChange >= min($usableCoins)) {
                $coin = \round(max($usableCoins), 2);
                $pendingChange = \round($pendingChange, 2);
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

    public function insert(float $coin): void
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
