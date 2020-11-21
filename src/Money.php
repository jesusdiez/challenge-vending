<?php
declare(strict_types=1);

namespace Vending;

final class Money
{
    private int $amountInCents;

    private function __construct(int $amountInCents)
    {
        $this->amountInCents = $amountInCents;
    }

    public static function fromString(string $value): self
    {
        return new self((int) \round((float) $value * 100, 2));
    }

    public function amountInCents(): int
    {
        return $this->amountInCents;
    }

    public function __toString(): string
    {
        return number_format($this->amountInCents() / 100, 2, '.', ',');
    }
}
