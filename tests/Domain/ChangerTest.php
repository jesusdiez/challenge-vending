<?php
declare(strict_types=1);

namespace Vending\Tests\Domain;

use PHPUnit\Framework\TestCase;
use Vending\Domain\Changer;
use Vending\Domain\Coin;
use Vending\Domain\Money;

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
        $actual = $this->sut->change(Money::fromString('1.40'));

        self::assertEquals($expected, $actual);
    }
}
