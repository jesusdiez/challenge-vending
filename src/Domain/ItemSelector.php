<?php
declare(strict_types=1);

namespace Vending\Domain;

use Lib\Enum;

/**
 * @method static ItemSelector WATER()
 * @method static ItemSelector JUICE()
 * @method static ItemSelector SODA()
 */
final class ItemSelector extends Enum
{
    public const WATER = 'WATER';
    public const JUICE = 'JUICE';
    public const SODA = 'SODA';
}
