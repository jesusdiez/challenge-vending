<?php
declare(strict_types=1);

namespace Vending\Domain;

use Lib\Enum;

/**
 * @method static Coin CENT5()
 * @method static Coin CENT10()
 * @method static Coin CENT25()
 * @method static Coin UNIT()
 */
final class Coin extends Enum
{
    public const CENT5 = 5;
    public const CENT10 = 10;
    public const CENT25 = 25;
    public const UNIT = 100;

    public static function fromString(string $value): self
    {
        return new self(Money::fromString($value)->cents());
    }

    public function __toString(): string
    {
        return (string) Money::fromInt($this->value());
    }

    public function moneyValue(): Money
    {
        return Money::fromInt($this->value());
    }
}
