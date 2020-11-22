<?php
declare(strict_types=1);

namespace Vending\Domain;

interface CoinHolder
{
    public function add(Coin $coin): void;

    public function get(Coin $coin): void;

    public function total(): Money;

    /** @return array|Coin[] */
    public function flush(): array;
}
