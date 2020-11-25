<?php
declare(strict_types=1);

namespace Vending\Application\Response;

use Vending\Domain\ItemSelector;

final class GetItemResponse
{
    private ItemSelector $itemSelector;
    private array $coins;

    public function __construct(ItemSelector $itemSelector, array $coins)
    {
        $this->itemSelector = $itemSelector;
        $this->coins = $coins;
    }

    public function itemSelector(): ItemSelector
    {
        return $this->itemSelector;
    }

    public function coins(): array
    {
        return $this->coins;
    }
}
