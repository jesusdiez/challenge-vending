<?php
declare(strict_types=1);

namespace Vending\Infrastructure\Persistence;

use Memcached;
use Vending\Domain\Machine;
use Vending\Domain\MachineRepository;

class MemcachedMachineRepository implements MachineRepository
{
    private Memcached $memcached;
    private string $namespace;

    public function __construct(Memcached $memcached, string $namespace = '')
    {
        $this->memcached = $memcached;
        $this->namespace = $namespace;
    }

    public function get(): Machine
    {
        return $this->unserialize($this->memcached->get($this->key()));
    }

    public function persist(Machine $machine): void
    {
        $this->memcached->set($this->key(), $this->serialize($machine));
    }

    public function delete(): void
    {
        $this->memcached->delete($this->key());
    }

    private function key(): string
    {
        return 'machine_' . $this->namespace;
    }

    // Memcached supports direct igbinary serialization if compiled with it, which is not assured.
    private function serialize(Machine $machine): string
    {
        return igbinary_serialize($machine);
    }

    private function unserialize(string $str): Machine
    {
        return igbinary_unserialize($str);
    }
}
