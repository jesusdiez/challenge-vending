<?php
declare(strict_types=1);

namespace Vending\Ui;

use Exception;
use Memcached;
use Vending\Application\VendingMachine;
use Vending\Domain\Coin;
use Vending\Domain\ItemSelector;
use Vending\Domain\Money;
use Vending\Infrastructure\Persistence\MemcachedMachineRepository;

final class AppCli
{
    private const RETURN_SUCCESS = 0;
    private const RETURN_FAILURE = 1;

    private $in;
    private $out;
    private $argv;
    private Memcached $memcached;
    private MemcachedMachineRepository $repo;
    private VendingMachine $vendingMachine;

    public function __construct($in, $out, array $argv)
    {
        $this->in = $in;
        $this->out = $out;
        $this->argv = $argv;

        $this->memcached = new Memcached();
        $this->memcached->addServer('memcached', 11211);
        $this->repo = new MemcachedMachineRepository($this->memcached);
        $this->vendingMachine = new VendingMachine($this->repo);
    }

    public function run(): int
    {
        $validCommands = ['insert', 'return', 'get', 'service-item', 'service-coin'];
        if (!isset($this->argv[1]) || !in_array($this->argv[1], $validCommands)) {
            $this->helpIntro();
            $this->print("Commands:");
            $this->helpInsert();
            $this->helpReturn();
            $this->helpGet();
            $this->helpServiceItem();
            $this->helpServiceCoin();

            return self::RETURN_SUCCESS;
        }

        try {
            switch ($this->argv[1]) {
                case 'insert':
                    return $this->runInsertAction($this->argv);
                case 'return':
                    return $this->runReturnAction();
                case 'get':
                    return $this->runGetAction($this->argv);
                case 'service-item':
                    return $this->runServiceItemAction($this->argv);
                case 'service-coin':
                    return $this->runServiceCoinAction($this->argv);
            }
        } catch (Exception $e) {
            $this->print("ERROR: %s", $e->getMessage());

            return self::RETURN_FAILURE;
        }

        return self::RETURN_SUCCESS;
    }

    private function runInsertAction(array $parameters): int
    {
        if (empty($parameters[2])) {
            $this->helpIntro();
            $this->helpInsert();

            return self::RETURN_SUCCESS;
        }
        $this->print('Insert coin: ');
        $coin = Coin::fromString($parameters[2]);
        $this->vendingMachine->insertCoin($coin);
        $this->print('%s coin accepted!', $coin);

        return self::RETURN_SUCCESS;
    }

    private function runReturnAction()
    {
        $response = $this->vendingMachine->returnCoins();
        if (empty($response->coins())) {
            $this->print('No coins to return');
        } else {
            $this->print('Return coins:');
            $this->print(implode(', ',$response->coins()));
        }

        return self::RETURN_SUCCESS;
    }

    private function runGetAction(array $parameters): int
    {
        if (empty($parameters[2])) {
            $this->helpIntro();
            $this->helpGet();

            return self::RETURN_SUCCESS;
        }
        $this->print('Get item: ');
        $item = ItemSelector::fromString($parameters[2]);
        $response = $this->vendingMachine->getItem($item);
        $this->print('Here is your %s', $response->itemSelector());
        if (!empty($response->coins())) {
            $this->print('Your change: %s', implode(', ', $response->coins()));
        }

        return self::RETURN_SUCCESS;
    }

    private function runServiceItemAction(array $parameters)
    {
        if (empty($parameters[2]) || empty($parameters[3])) {
            $this->helpIntro();
            $this->helpServiceItem();

            return self::RETURN_SUCCESS;
        }
        $item = ItemSelector::fromString($parameters[2]);
        $count = (int) $parameters[3];
        $this->vendingMachine->serviceSetItem($item, $count);
        $this->print('Service. Set item %s count to %s. OK!', $item, $count);

        return self::RETURN_SUCCESS;
    }

    private function runServiceCoinAction(array $parameters)
    {
        if (empty($parameters[2]) || empty($parameters[3])) {
            $this->helpIntro();
            $this->helpServiceCoin();

            return self::RETURN_SUCCESS;
        }
        $coin = Coin::fromString($parameters[2]);
        $count = (int) $parameters[3];
        $this->vendingMachine->serviceSetChange($coin, $count);
        $this->print('Service. Set coin %s count to %s. OK!', $coin, $count);

        return self::RETURN_SUCCESS;
    }

    private function print(string $text, $param1 = null, $param2 = null)
    {
        \fprintf($this->out, $text . PHP_EOL, $param1, $param2);
    }

    private function helpIntro()
    {
        $this->print("Vending Machine");
        $this->print("---------------");
        $this->print("Syntax:\t%s command <parameters>", $this->argv[0]);
        $this->print("");
    }

    private function helpInsert(): void
    {
        $this->print("insert <coin>\tinserts a coin in the machine");
        $this->print("\t<coin>\tin numeric format, english decimal separator. Valid values: %s", $this->validCoins());
        $this->print("");
    }

    private function helpReturn(): void
    {
        $this->print("return\treturns coins inserted previously");
        $this->print("");
    }

    private function helpGet(): void
    {
        $this->print("get <item>\tretrieves an item from the machine after inserting the coins needed");
        $this->print("\t<item>\tselector for the item. Valid values: %s", $this->validItems());
        $this->print("");
    }

    private function helpServiceItem(): void
    {
        $this->print("service-item <item> <count>\tsets the item count in the machine inventory");
        $this->print("\t<item>\tselector for the item. Valid values: %s", $this->validItems());
        $this->print("\t<count>\titem count, any natural int number.");
        $this->print("");
    }

    private function helpServiceCoin(): void
    {
        $this->print("service-coin <coin> <count>\tsets the coin count in the machine coin holder");
        $this->print("\t<coin>\tin numeric format, english decimal separator. Valid values: %s", $this->validCoins());
        $this->print("\t<count>\titem count, any natural int number.");
        $this->print("");
    }

    private function validCoins(): string
    {
        return implode(', ', array_map(fn($val) => (string) Money::fromInt($val), Coin::values()));
    }

    private function validItems(): string
    {
        return implode(', ', ItemSelector::values());
    }
}
