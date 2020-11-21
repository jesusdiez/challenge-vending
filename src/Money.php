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

    public static function fromInt(int $amountInCents): self
    {
        return new self($amountInCents);
    }

    public function amountInCents(): int
    {
        return $this->amountInCents;
    }

    public function __toString(): string
    {
        return number_format($this->amountInCents() / 100, 2, '.', ',');
    }

    public function add(Money $money): Money
    {
        return new Money($this->amountInCents + $money->amountInCents());
    }

    public function substract(Money $money)
    {
        return new Money($this->amountInCents - $money->amountInCents());
    }
}
