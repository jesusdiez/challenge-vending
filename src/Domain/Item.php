<?php
declare(strict_types=1);

namespace Vending\Domain;

final class Item
{
    private ItemSelector $selector;
    private Money $price;
    private int $count;

    public function __construct(ItemSelector $selector, Money $price, int $count = 0)
    {
        $this->selector = $selector;
        $this->price = $price;
        $this->count = $count;
    }

    public function selector(): ItemSelector
    {
        return $this->selector;
    }

    public function price(): Money
    {
        return $this->price;
    }

    public function count(): int
    {
        return $this->count;
    }

    public function setCount(int $count): void
    {
        $this->count = $count;
    }
}
