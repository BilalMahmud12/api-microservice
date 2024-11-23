<?php

namespace App\DTOs\Customer;

class CreateCustomerDTO
{
    public $name;
    public $surname;
    public $balance;

    public function __construct(string $name, string $surname)
    {
        $this->name = $name;
        $this->surname = $surname;
        $this->balance = 0;
    }
}
