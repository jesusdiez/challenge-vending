<?php
declare(strict_types=1);

namespace Vending\Tests\Domain;

use PHPUnit\Framework\TestCase;
use Vending\Domain\Coin;
use Vending\Domain\CoinHolder;

class CoinHolderTest extends TestCase
{
    private CoinHolder $sut;

    protected function setUp(): void
    {
        $this->sut = CoinHolder::createEmpty();
    }

    public function testAddOneAndFlush()
    {
        $this->sut->add(Coin::CENT25());
        self::assertEquals([Coin::CENT25()], $this->sut->flush());
    }

    public function testAddArrayAndFlush()
    {
        $coinArray = [Coin::CENT25(), Coin::CENT25(), Coin::CENT10()];
        $this->sut->addArray($coinArray);
        self::assertEquals($coinArray, $this->sut->flush());
    }

    public function testAddMultipleAndFlush()
    {
        $this->sut->add(Coin::CENT25());
        $this->sut->add(Coin::CENT5());
        $this->sut->add(Coin::CENT10());
        self::assertEquals([Coin::CENT25(), Coin::CENT5(), Coin::CENT10()], $this->sut->flush());
    }

    public function testCleanAfterFlush()
    {
        $this->sut->add(Coin::CENT5());
        $this->sut->flush();
        self::assertEquals([], $this->sut->flush());
    }
}
