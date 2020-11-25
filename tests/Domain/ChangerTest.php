<?php
declare(strict_types=1);

namespace Vending\Tests\Domain;

use PHPUnit\Framework\TestCase;
use Vending\Domain\Changer;
use Vending\Domain\Coin;
use Vending\Domain\CoinHolder;
use Vending\Domain\Money;
use Vending\Infrastructure\InMemoryCoinHolder;

class ChangerTest extends TestCase
{
    private Changer $sut;

    protected function setUp(): void
    {
        $this->sut = new Changer($this->getCoinHolderWithOneCoinOfEach());
    }

    public function testChange()
    {
        $expected = [Coin::UNIT(), Coin::CENT25(), Coin::CENT10(), Coin::CENT5()];
        $actual = $this->sut->change(Money::fromString('1.40'));

        self::assertEquals($expected, $actual);
    }

    private function getCoinHolderWithOneCoinOfEach(): CoinHolder
    {
        $coinHolder = CoinHolder::createEmpty();
        $coinHolder->addArray(array_map(fn(int $coinVal): Coin => Coin::fromInt($coinVal), Coin::values()));

        return $coinHolder;
    }
}
