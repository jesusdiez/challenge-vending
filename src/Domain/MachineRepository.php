<?php
declare(strict_types=1);

namespace Vending\Domain;

interface MachineRepository
{
    public function get(): Machine;
    public function persist(Machine $machine): void;
    public function delete(): void;
}
