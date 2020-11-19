<?php
declare(strict_types=1);

namespace Vending;

use InvalidArgumentException;

final class Item
{
    public const WATER = 'WATER';
    public const SODA = 'SODA';
    public const JUICE = 'JUICE';
    private string $value;

    public function __construct(string $value)
    {
        $this->guardIsValid($value);
        $this->value = $value;
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public static function options(): array
    {
        return [
            'WATER' => static::WATER,
            'SODA' => static::SODA,
            'JUICE' => static::JUICE,
        ];
    }

    public static function values(): array
    {
        return \array_values(self::options());
    }

    public static function isValid($value): bool
    {
        return \in_array($value, self::values());
    }

    public function __toString(): string
    {
        return $this->value();
    }

    public function value(): string
    {
        return $this->value;
    }

    private function guardIsValid(string $value)
    {
        if (!self::isValid($value)) {
            throw new InvalidArgumentException(sprintf('%s is not a valid value', $value));
        }
    }
}
