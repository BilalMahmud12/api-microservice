<?php

namespace App\Services;
use App\Contracts\AccountRepositoryInterface;
use Illuminate\Support\Facades\DB;
use App\Exceptions\ValidationException;

class AccountService
{
    protected $accountRepository;

    public function __construct(AccountRepositoryInterface $accountRepository)
    {
        $this->accountRepository = $accountRepository;
    }

    public function getBalance(int $id)
    {
        if ($id <= 0) {
            throw new ValidationException("Invalid customer ID", 422);
        }

        if (!$this->accountRepository->exists($id)) {
            throw new ValidationException("Customer not found", 404);
        }

        return $this->accountRepository->getBalance($id);
    }

    public function deposit(int $id, int $funds): bool
    {
        if (!$this->accountRepository->exists($id)) {
            throw new ValidationException("Customer not found", 404);
        }

        if ($funds <= 0) {
            throw new ValidationException("Deposit amount must be greater than zero", 422);
        }

        DB::beginTransaction();
        try {
            $customer = $this->accountRepository->deposit($id, $funds);

            if (!$customer) {
                throw new \Exception("Deposit failed");
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function withdraw(int $id, int $funds): bool
    {
        if (!$this->accountRepository->exists($id)) {
            throw new ValidationException("Customer not found", 404);
        }

        if ($funds <= 0) {
            throw new ValidationException("Withdraw amount must be greater than zero", 422);
        }

        DB::beginTransaction();
        try {
            $customer = $this->accountRepository->withdraw($id, $funds);

            if (!$customer) {
                throw new \Exception("withdraw failed");
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function transfer(array $data): bool
    {
        if (!isset($data['from'], $data['to'], $data['funds'])) {
            throw new ValidationException("Invalid input data: 'from', 'to', and 'funds' are required.", 422);
        }

        if ($data['from'] == $data['to']) {
            throw new ValidationException("Cannot transfer funds to the same account.", 422);
        }

        if ($data['funds'] <= 0) {
            throw new ValidationException("Transfer amount must be greater than zero.", 422);
        }

        if (!$this->accountRepository->exists($data['from'])) {
            throw new ValidationException("Source customer not found.", 404);
        }

        if (!$this->accountRepository->exists($data['to'])) {
            throw new ValidationException("Target customer not found.", 404);
        }

        DB::beginTransaction();
        try {
            $sourceCustomer = $this->accountRepository->withdraw($data['from'], $data['funds']);
            if (!$sourceCustomer) {
                throw new \Exception("Withdrawal from source customer failed.");
            }

            $targetCustomer = $this->accountRepository->deposit($data['to'], $data['funds'], $is_transfer = true);
            if (!$targetCustomer) {
                throw new \Exception("Deposit to target customer failed.");
            }

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function rebuildCustomerAccountBalance(int $id)
    {
        DB::beginTransaction();

        try {
            $customer = $this->accountRepository->getCustomerWithLock($id);

            if (!$customer) {
                throw new \Exception('Customer not found.');
            }

            $balance = $this->accountRepository->getTransactionsSum($id);

            $success = $this->accountRepository->updateCustomerBalance($customer, $balance ?? 0);

            if (!$success) {
                throw new \Exception('Failed to update customer balance.');
            }

            DB::commit();

            return $balance ?? 0;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
