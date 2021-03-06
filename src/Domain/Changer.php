<?php
declare(strict_types=1);

namespace Vending\Domain;

final class Changer
{
    private CoinHolder $availableCoins;

    public function __construct(CoinHolder $availableCoins)
    {
        $this->availableCoins = $availableCoins;
    }

    /** @return Coin[]|null */
    public function change(Money $amount): ?array
    {
        $pendingChange = $amount;
        $changeCoins = [];
        $usableCoins = $this->availableCoins->sortedValues();
        sort($usableCoins);

        while (!empty($usableCoins) && $pendingChange->greaterOrEqualThan(Money::fromInt(min($usableCoins)))) {
            $currentCoinCents = max($usableCoins);
            $currentCoin = Coin::fromInt($currentCoinCents);
            if ($pendingChange->greaterOrEqualThan($currentCoin->toMoney())
                && $this->availableCoins->has($currentCoin))
            {
                $pendingChange = $pendingChange->substract($currentCoin->toMoney());
                $this->availableCoins->get($currentCoin);
                array_push($changeCoins, $currentCoin);
            } else {
                array_pop($usableCoins);
            }
        }
        if ($pendingChange->greaterThan(Money::fromInt(0))) {
            return null;
        }

        return $changeCoins;
    }
}
