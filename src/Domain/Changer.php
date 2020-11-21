<?php
declare(strict_types=1);

namespace Vending\Domain;

final class Changer
{
    private array $availableChange = [
        Coin::UNIT => 0,
        Coin::CENT25 => 0,
        Coin::CENT10 => 0,
        Coin::CENT5 => 0,
    ];

    public function hasChange(Money $amount): bool
    {
        // FIXME Implement, this must do a real system state check
        return true;
    }

    /** @return array|Coin[] */
    public function change(Money $amount): array
    {
        $pendingChange = $amount;
        $response = [];
        $usableCoins = Coin::values();
        sort($usableCoins);
        while ($pendingChange->cents() >= min($usableCoins)) {
            $coinAmountInCents = max($usableCoins);
            if ($pendingChange->cents() >= $coinAmountInCents) {
                $pendingChange = $pendingChange->substract(Money::fromInt($coinAmountInCents));

                // FIXME This also must use a real state check
                $this->availableChange[$coinAmountInCents]--;
                array_push($response, Coin::fromString((string) Money::fromInt($coinAmountInCents)));
            } else {
                array_pop($usableCoins);
            }
        }

        return $response;
    }
}
