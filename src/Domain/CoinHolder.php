<?php
declare(strict_types=1);

namespace Vending\Domain;

interface CoinHolder
{
    public function add(Coin $coin): void;

    public function addArray(array $coins): void;

    /** @return array|Coin[] */
    public function flush(): array;

    public function get(Coin $coin): void;

    /** @return array|Coin[] */
    public function getAll(): array;

    public function retrieveArray(array $coins): void;

    public function sortedValues(): array;

    public function total(): Money;
}
