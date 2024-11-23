<?php

namespace App\Services;

use App\Contracts\CustomerRepositoryInterface;
use App\DTOs\Customer\CreateCustomerDTO;

class CustomerService
{
    protected $customerRepository;

    public function __construct(CustomerRepositoryInterface $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public function getAllCustomers(int $page, int $perPage)
    {
        return $this->customerRepository->getAllCustomers($page, $perPage);
    }

    public function createCustomer(array $data)
    {
        $customerDTO = new CreateCustomerDTO(
            $data['name'],
            $data['surname']
        );

        return $this->customerRepository->createCustomer($customerDTO);
    }

    public function getCustomerById(int $id)
    {
        return $this->customerRepository->getCustomerById($id);
    }

    public function updateCustomer(int $id, array $data)
    {
        return $this->customerRepository->updateCustomer($id, $data);
    }

    public function deleteCustomer(int $id)
    {
        return $this->customerRepository->deleteCustomer($id);
    }
}
