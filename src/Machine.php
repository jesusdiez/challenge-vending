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
    private array $productInventory = [
        'SODA' => 1,
        'JUICE' => 1,
        'WATER' => 1,
    ];

    public function insert(float $coin): void
    {
        array_push($this->coinBuffer, $coin);
    }

    public function get(string $product): array
    {
        $moneyInserted = \array_sum($this->coinBuffer);
        $change = $moneyInserted - $this->productPrices[$product];
        $response = [];
        if ($change >= 0) {
            $this->productInventory[$product]--;
            array_push($response, $product);
        }
        if ($change > 0) {
            $usableCoins = self::$coins;
            sort($usableCoins);
            while ($change >= min($usableCoins)) {
                $coin = \round(max($usableCoins), 2);
                $change = \round($change, 2);
                if ($change >= $coin) {
                    $change -= $coin;
                    array_push($response, $coin);
                } else {
                    array_pop($usableCoins);
                }
            }
        }

        return $response;
    }

    public function returnCoin(): array
    {
        $response = $this->coinBuffer;
        $this->coinBuffer = [];

        return $response;
    }

}
