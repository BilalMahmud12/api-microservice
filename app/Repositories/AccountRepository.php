<?php

namespace App\Repositories;

use App\Contracts\AccountRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use App\Exceptions\ValidationException;
use App\Models\Customer;
use App\Models\Transaction;

class AccountRepository implements AccountRepositoryInterface
{
    public function exists(int $id): bool
    {
        return Customer::find($id) ? true : false;
    }

    public function getCustomerWithLock(int $id): ?Customer
    {
        return Customer::lockForUpdate()->find($id);
    }

    public function getAllCustomersWithLock(int $offset, int $limit): Collection
    {
        return Customer::lockForUpdate()
            ->skip($offset)
            ->take($limit)
            ->get();
    }

    public function getBalance(int $id): int
    {
        $customer = Customer::find($id);

        if (!$customer) {
            throw new \Exception('Customer not found.');
        }

        return $customer->balance;
    }

    public function deposit(int $id, int $funds, bool $is_transfer = false): Customer
    {
        $customer = $this->getCustomerWithLock($id);

        if (!$customer) {
            throw new \Exception('Customer not found.');
        }

        $balanceBefore = $customer->balance;
        $balanceAfter = $customer->balance + $funds;
        $customer->balance = $balanceAfter;

        if (!$customer->save()) {
            throw new \Exception('Failed to update customer balance.');
        }

        if ($customer->balance !== $balanceAfter) {
            throw new \Exception('Failed to update customer balance.');
        }

        $transaction = $this->recordAccountTransaction(
            $customer->id,
            $is_transfer ? Transaction::TYPE_TRANSFER : Transaction::TYPE_DEPOSIT,
            $funds,
            $balanceBefore,
            $balanceAfter
        );

        if (!$transaction) {
            throw new \Exception('Failed to record deposit transaction.');
        }

        return $customer;
    }

    public function withdraw(int $id, int $funds): Customer
    {
        $customer = $this->getCustomerWithLock($id);

        if (!$customer) {
            throw new \Exception('Customer not found.');
        }

        if ($customer->balance < $funds) {
            throw new ValidationException("Insufficient balance for withdrawal.", 422);
        }

        $balanceBefore = $customer->balance;
        $balanceAfter = $customer->balance - $funds;
        $customer->balance = $balanceAfter;

        if (!$customer->save()) {
            throw new \Exception('Failed to update customer balance.');
        }

        if ($customer->balance !== $balanceAfter) {
            throw new \Exception('Failed to update customer balance.');
        }

        $transaction = $this->recordAccountTransaction(
            $customer->id,
            Transaction::TYPE_WITHDRAW,
            $funds,
            $balanceBefore,
            $balanceAfter
        );

        if (!$transaction) {
            throw new \Exception('Failed to record withdraw transaction.');
        }

        return $customer;
    }

    private function recordAccountTransaction(int $id, string $type, int $amount, int $balanceBefore, int $balanceAfter)
    {
        $transaction = new Transaction();
        $transaction->customer_id = $id;
        $transaction->type = $type;
        $transaction->amount = $amount;
        $transaction->balance_before = $balanceBefore;
        $transaction->balance_after = $balanceAfter;

        return $transaction->save() ? $transaction : false;
    }

    public function getTransactionsSum(int $id): float
    {
        $incomingSum = Transaction::where('customer_id', $id)
            ->whereIn('type', [Transaction::TYPE_DEPOSIT, Transaction::TYPE_TRANSFER])
            ->where('balance_after', '>', 'balance_before')
            ->sum('amount');

        $outgoingSum = Transaction::where('customer_id', $id)
            ->whereIn('type', [Transaction::TYPE_WITHDRAW])
            ->where('balance_before', '>', 'balance_after')
            ->sum('amount');

        $balance = $incomingSum - $outgoingSum;

        return $balance;
    }

    public function updateCustomerBalance(Customer $customer, float $balance): bool
    {
        $customer->balance = $balance;
        return $customer->save();
    }
}
