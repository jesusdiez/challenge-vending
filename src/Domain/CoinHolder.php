<?php
declare(strict_types=1);

namespace Vending\Domain;

interface CoinHolder
{
    public function add(Coin $coin): void;

    public function addArray(array $coins): void;

    public function sortedValues(): array;

    public function get(Coin $coin): void;

    /** @return array|Coin[] */
    public function getAll(): array;

    public function getArray(array $coins): void;

    public function total(): Money;

    /** @return array|Coin[] */
    public function flush(): array;
}
