<?php
declare(strict_types=1);

namespace Vending\Tests;

use Vending\Changer;
use PHPUnit\Framework\TestCase;
use Vending\Coin;

class ChangerTest extends TestCase
{
    private Changer $sut;

    protected function setUp(): void
    {
        $this->sut = new Changer();
    }

    public function testChange()
    {
        $expected = [Coin::UNIT(), Coin::CENT25(), Coin::CENT10(), Coin::CENT5()];
        $actual = $this->sut->change(140);

        self::assertEquals($expected, $actual);
    }
}
