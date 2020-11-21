<?php
declare(strict_types=1);

namespace Vending\Infrastructure;

use RuntimeException;
use Vending\Domain\Inventory;
use Vending\Domain\Item;
use Vending\Domain\ItemSelector;
use Vending\Domain\Money;

final class InMemoryInventory implements Inventory
{
    private array $storage = [];

    public function __construct(array $items = [])
    {
        // FIXME: Initialization, move outside
        $prices = [
            ItemSelector::JUICE => Money::fromString('1.00'),
            ItemSelector::SODA => Money::fromString('1.50'),
            ItemSelector::WATER => Money::fromString('0.65'),
        ];
        \array_walk(
            $items,
            fn($units, $item) => $this->create(new Item(ItemSelector::fromString($item), $prices[$item], $units))
        );
    }

    public function hasStock(ItemSelector $itemSelector): bool
    {
        $item = $this->storage[$itemSelector->value()] ?? false;

        return $item ? ($item->count() > 0) : false;
    }

    public function sell(ItemSelector $itemSelector): void
    {
        if (!$this->hasStock($itemSelector)) {
            throw new RuntimeException('No stock');
        }
        $item = $this->storage[$itemSelector->value()];
        $item->setCount($item->count() - 1);
    }

    public function buy(ItemSelector $item, int $units = 1): void
    {
        $item = $this->storage[$item->value()];
        $item->setCount($item->count() + $units);
    }

    public function create(Item $item): void
    {
        if ($this->hasStock($item->selector())) {
            $existingItem = $this->storage[$item->selector()->value()];
            $existingItem->setCount($existingItem->count() + $item->count());

            return;
        }
        $this->storage[$item->selector()->value()] = $item;
    }

    public function get(ItemSelector $itemSelector): Item
    {
        if (!$this->hasStock($itemSelector)) {
            throw new RuntimeException('No stock');
        }
        return $this->storage[$itemSelector->value()];
    }
}

