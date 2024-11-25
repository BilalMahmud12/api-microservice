<?php

namespace App\Contracts;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Collection;

interface AccountRepositoryInterface
{
    public function exists(int $id): bool;

    public function getCustomerWithLock(int $id): ?Customer;

    public function getAllCustomersWithLock(int $offset, int $limit): Collection;

    public function getBalance(int $id): int;

    public function deposit(int $id, int $funds, bool $is_transfer = false): Customer;

    public function withdraw(int $id, int $funds): Customer;

    public function getTransactionsSum(int $id): float;

    public function updateCustomerBalance(Customer $customer, float $balance): bool;
}
