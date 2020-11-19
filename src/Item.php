<?php
declare(strict_types=1);

namespace Vending;

use Lib\Enum;

/**
 * @method static Item WATER()
 * @method static Item JUICE()
 * @method static Item SODA()
 */
final class Item extends Enum
{
    public const WATER = 'WATER';
    public const JUICE = 'JUICE';
    public const SODA = 'SODA';
}
