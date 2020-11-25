<?php
declare(strict_types=1);

namespace Vending\Application\Response;

final class ReturnCoinsResponse
{
    private array $coins;

    public function __construct(array $coins)
    {
        $this->coins = $coins;
    }

    public function coins(): array
    {
        return $this->coins;
    }
}
