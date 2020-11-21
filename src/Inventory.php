<?php
declare(strict_types=1);

namespace Vending;

final class Inventory
{
    private array $storage = [];
    private array $prices;

    public function __construct(array $items = [])
    {
        $this->prices = [
            Item::JUICE => Money::fromString('1.00'),
            Item::SODA => Money::fromString('1.50'),
            Item::WATER => Money::fromString('0.65'),
        ];
        \array_walk($items, fn($units, $item) => $this->addStock(Item::fromString($item), $units));
    }

    public function has(Item $item): bool
    {
        return ($this->storage[$item->value()] ?? 0) > 0;
    }

    public function hasPrice(Item $item): bool
    {
        return \array_key_exists($item->value(), $this->prices);
    }

    public function sell(Item $item): void
    {
        if (!$this->has($item)) {
            throw new \RuntimeException('No stock');
        }
        $this->storage[$item->value()]--;
    }

    public function addStock(Item $item, int $units = 1): void
    {
        $this->storage[$item->value()] = ($this->storage[$item->value()] ?? 0) + $units;
    }

    public function price(Item $item): Money
    {
        if (!$this->hasPrice($item)) {
            throw new \RuntimeException('No price set for item');
        }

        return $this->prices[$item->value()];
    }
}

