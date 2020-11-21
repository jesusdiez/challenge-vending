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
    public function change(Money $amount): array
    {
        $pendingChange = $amount;
        $response = [];
        $usableCoins = Coin::values();
        sort($usableCoins);
        while ($pendingChange->amountInCents() >= min($usableCoins)) {
            $coinAmountInCents = max($usableCoins);
            if ($pendingChange->amountInCents() >= $coinAmountInCents) {
                $pendingChange = $pendingChange->substract(Money::fromInt($coinAmountInCents));
                $this->availableChange[$coinAmountInCents]--;
                array_push($response, Coin::fromString((string) Money::fromInt($coinAmountInCents)));
            } else {
                array_pop($usableCoins);
            }
        }

        return $response;
    }
}
