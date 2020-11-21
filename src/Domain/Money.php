<?php
declare(strict_types=1);

namespace Vending\Domain;

final class Money
{
    private int $cents;

    private function __construct(int $cents)
    {
        $this->cents = $cents;
    }

    public static function fromString(string $value): self
    {
        return new self((int) \round((float) $value * 100, 2));
    }

    public static function fromInt(int $amountInCents): self
    {
        return new self($amountInCents);
    }

    public function cents(): int
    {
        return $this->cents;
    }

    public function __toString(): string
    {
        return number_format($this->cents() / 100, 2, '.', ',');
    }

    public function add(Money $money): self
    {
        return new self($this->cents + $money->cents());
    }

    public function substract(Money $money): self
    {
        return new self($this->cents - $money->cents());
    }

    public function multiply(int $multiplier): self
    {
        return new self($this->cents * $multiplier);
    }
}
