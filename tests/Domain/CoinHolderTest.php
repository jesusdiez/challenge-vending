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
        $this->sut = new CoinHolder();
    }

    public function testAddOneAndFlush()
    {
        $this->sut->addCoin(Coin::CENT25());
        self::assertEquals([Coin::CENT25()], $this->sut->flush());
    }

    public function testAddMultipleAndFlush()
    {
        $this->sut->addCoin(Coin::CENT25());
        $this->sut->addCoin(Coin::CENT5());
        $this->sut->addCoin(Coin::CENT10());
        self::assertEquals([Coin::CENT25(), Coin::CENT5(), Coin::CENT10()], $this->sut->flush());
    }

    public function testCleanAfterFlush()
    {
        $this->sut->addCoin(Coin::CENT5());
        $this->sut->flush();
        self::assertEquals([], $this->sut->flush());
    }
}
