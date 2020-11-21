<?php
declare(strict_types=1);

namespace Vending;

final class Changer
{
    private array $availableChange = [
        Coin::UNIT => 0,
        Coin::CENT25 => 0,
        Coin::CENT10 => 0,
        Coin::CENT5 => 0,
    ];

    /** @return array|Coin[] */
    public function change(int $amount): array
    {
        $pendingChange = $amount;
        $response = [];
        $usableCoins = Coin::values();
        sort($usableCoins);
        while ($pendingChange >= min($usableCoins)) {
            $coin = max($usableCoins);
            if ($pendingChange >= $coin) {
                $pendingChange -= $coin;
                $this->availableChange[$coin]--;
                array_push($response, Coin::fromString((string) ($coin/100)));
            } else {
                array_pop($usableCoins);
            }
        }

        return $response;
    }
}
