<?php
declare(strict_types=1);

namespace Vending\Domain;

final class CoinHolder
{
    private array $map = [];

    public function add(Coin $coin): void
    {
        $this->map[$coin->value()] = ($this->map[$coin->value()] ?? 0) + 1;
    }

    public function addArray(array $coins): void
    {
        \array_walk($coins, fn(Coin $coin) => $this->add($coin));
    }

    public function flush(): array
    {
        $output = $this->getAll();
        $this->empty();;

        return $output;
    }

    public function get(Coin $coin): void
    {
        if (!$this->has($coin)) {
            throw new \RuntimeException(sprintf('No coin %s', $coin));
        }
        $this->map[$coin->value()] = ($this->map[$coin->value()] ?? 0) - 1;
    }

    public function getAll(): array
    {
        return array_reduce(
            array_keys($this->map),
            function (array $carry, $coinValue) {
                return array_merge(
                    $carry,
                    array_fill(0, $this->map[$coinValue], Coin::fromString((string) Money::fromInt($coinValue)))
                );
            },
            []
        );
    }

    public function has(Coin $coin): bool
    {
        return ($this->map[$coin->value()] ?? 0) > 0;
    }

    public function retrieveArray(array $coins): void
    {
        array_walk($coins, fn(Coin $coin) => $this->get($coin));
    }

    public function set(Coin $coin, int $count): void
    {
        $this->map[$coin->value()] = $count;
    }

    public function sortedValues(): array
    {
        $values = \array_keys($this->map);
        sort($values);

        return $values;
    }

    public function total(): Money
    {
        return array_reduce(
            array_keys($this->map),
            fn(Money $carry, $coinValue) => $carry->add(Money::fromInt($coinValue)->multiply($this->map[$coinValue])),
            Money::fromInt(0)
        );
    }

    private function empty(): void
    {
        $this->map = [];
    }
}
