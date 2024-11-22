<?php

namespace App\Contracts;

interface AccountRepositoryInterface
{
    public function getBalance(int $id);

    public function deposit(int $id, int $funds);

    public function withdraw(int $id, int $funds);

    public function transfer(array $data);
}
