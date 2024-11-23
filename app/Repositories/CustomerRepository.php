<?php

namespace App\Repositories;

use App\Models\Customer;
use App\Contracts\CustomerRepositoryInterface;
use App\DTOs\Customer\CreateCustomerDTO;

class CustomerRepository implements CustomerRepositoryInterface
{
    public function getAllCustomers(int $page, int $perPage)
    {
        //
    }

    public function getCustomerById(int $id)
    {
        //
    }

    public function createCustomer(CreateCustomerDTO $data)
    {
        //
    }

    public function updateCustomer(int $id, array $data)
    {
        //
    }

    public function deleteCustomer(int $id)
    {
        //
    }
}
