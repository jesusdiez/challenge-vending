<?php
declare(strict_types=1);

namespace Vending;

use InvalidArgumentException;

final class Coin
{
    public const CENT5 = 5;
    public const CENT10 = 10;
    public const CENT25 = 25;
    public const UNIT = 100;
    private int $value;

    public function __construct(int $value)
    {
        $this->guardIsValid($value);
        $this->value = $value;
    }

    public static function fromString(string $value): self
    {
        return new self((int) ((float) $value * 100));
    }

    public static function options(): array
    {
        return [
            'CENT5' => static::CENT5,
            'CENT10' => static::CENT10,
            'CENT25' => static::CENT25,
            'UNIT' => static::UNIT,
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

    private function guardIsValid(int $value)
    {
        if (!self::isValid($value)) {
            throw new InvalidArgumentException(sprintf('%s is not a valid value', $value));
        }
    }
}
