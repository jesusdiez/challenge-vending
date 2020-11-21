<?php
declare(strict_types=1);

namespace Vending\Domain;

interface Inventory
{
    public function buy(ItemSelector $item, int $number): void;

    public function create(Item $item): void;

    public function get(ItemSelector $itemSelector): Item;

    public function hasStock(ItemSelector $itemSelector): bool;

    public function sell(ItemSelector $itemSelector): void;
}
