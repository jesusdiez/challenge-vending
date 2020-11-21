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
            ItemSelector::JUICE => Money::fromString('1.00'),
            ItemSelector::SODA => Money::fromString('1.50'),
            ItemSelector::WATER => Money::fromString('0.65'),
        ];
        \array_walk($items, fn($units, $item) => $this->addStock(ItemSelector::fromString($item), $units));
    }

    public function has(ItemSelector $item): bool
    {
        return ($this->storage[$item->value()] ?? 0) > 0;
    }

    public function hasPrice(ItemSelector $item): bool
    {
        return \array_key_exists($item->value(), $this->prices);
    }

    public function sell(ItemSelector $item): void
    {
        if (!$this->has($item)) {
            throw new \RuntimeException('No stock');
        }
        $this->storage[$item->value()]--;
    }

    public function addStock(ItemSelector $item, int $units = 1): void
    {
        $this->storage[$item->value()] = ($this->storage[$item->value()] ?? 0) + $units;
    }

    public function price(ItemSelector $item): Money
    {
        if (!$this->hasPrice($item)) {
            throw new \RuntimeException('No price set for item');
        }

        return $this->prices[$item->value()];
    }
}

