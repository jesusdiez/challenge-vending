<?php
declare(strict_types=1);

namespace Lib;

use InvalidArgumentException;
use ReflectionClass;

abstract class Enum
{
    private $value;

    public function __construct($value)
    {
        static::guardIsValid($value);
        $this->value = $value;
    }

    public static function __callStatic(string $name, $arguments)
    {
        $constant = static::class . '::' . $name;

        return new static(defined($constant) ? constant($constant) : null);
    }

    public static function fromString(string $value): self
    {
        return new static($value);
    }

    public static function values(): array
    {
        $class = \get_called_class();
        $reflected = new ReflectionClass($class);

        return $reflected->getConstants();
    }

    protected static function isValid($value): bool
    {
        return in_array($value, array_values(static::values()), true);
    }

    public function __toString()
    {
        return (string) $this->value();
    }

    public function equals($newValue): bool
    {
        return $newValue instanceof static && $newValue->value() === $this->value();
    }

    public function value()
    {
        return $this->value;
    }

    protected function guardIsValid($value)
    {
        if (!static::isValid($value)) {
            throw new InvalidArgumentException(sprintf('%s is not a valid value', (string) $value));
        }
    }
}
