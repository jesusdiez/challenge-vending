<?php
declare(strict_types=1);

namespace Vending\Infrastructure;

use Vending\Domain\Coin;
use Vending\Domain\CoinHolder;
use Vending\Domain\Money;

final class InMemoryCoinHolder implements CoinHolder
{
    private array $map = [];

    public function add(Coin $coin): void
    {
        $this->map[$coin->value()] = ($this->map[$coin->value()] ?? 0) + 1;
    }

    public function has(Coin $coin): bool
    {
        return ($this->map[$coin->value()] ?? 0) > 0;
    }

    public function get(Coin $coin): void
    {
        if (!$this->has($coin)) {
            throw new \RuntimeException('No coins');
        }
        $this->map[$coin->value()] = ($this->map[$coin->value()] ?? 0) - 1;
    }

    public function total(): Money
    {
        return array_reduce(
            array_keys($this->map),
            fn(Money $carry, $coinValue) => $carry->add(Money::fromInt($coinValue)->multiply($this->map[$coinValue])),
            Money::fromInt(0)
        );
    }

    public function flush(): array
    {
        $output = array_reduce(
            array_keys($this->map),
            function (array $carry, $coinValue) {
                return array_merge(
                    $carry,
                    \array_fill(0, $this->map[$coinValue], Coin::fromString((string) Money::fromInt($coinValue)))
                );
            },
            []
        );
        $this->map = [];

        return $output;
    }
}
