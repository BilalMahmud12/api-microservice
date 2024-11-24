<?php

namespace App\Services;
use App\Contracts\AccountRepositoryInterface;

class AccountService
{
    protected $accountRepository;

    public function __construct(AccountRepositoryInterface $accountRepository)
    {
        $this->accountRepository = $accountRepository;
    }

    public function getBalance(int $id)
    {
        return $this->accountRepository->getBalance($id);
    }

    public function deposit(int $id, int $funds)
    {
        return $this->accountRepository->deposit($id, $funds);
    }

    public function withdraw(int $id, int $funds)
    {
        return $this->accountRepository->withdraw($id, $funds);
    }

    public function transfer(array $data)
    {
        return $this->accountRepository->transfer($data);
    }
}
