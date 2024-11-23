<?php

namespace App\Contracts;

use App\DTOs\Customer\CreateCustomerDTO;

interface CustomerRepositoryInterface
{
    public function getAllCustomers(int $page, int $perPage);

    public function getCustomerById(int $id);

    public function createCustomer(CreateCustomerDTO $data);

    public function updateCustomer(int $id, array $data);

    public function deleteCustomer(int $id);
}
