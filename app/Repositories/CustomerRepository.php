<?php

namespace App\Repositories;

use App\Models\Customer;
use App\Contracts\CustomerRepositoryInterface;
use App\DTOs\Customer\CreateCustomerDTO;

class CustomerRepository implements CustomerRepositoryInterface
{
    public function getAllCustomers(int $page, int $perPage)
    {
        return Customer::paginate($perPage, ['*'], 'page', $page);
    }

    public function getCustomerById(int $id)
    {
        return Customer::find($id);
    }

    public function createCustomer(CreateCustomerDTO $data)
    {
        $customer = new Customer();
        $customer->name = $data->name;
        $customer->surname = $data->surname;
        $customer->balance = $data->balance;

        $customer->save();

        return $customer;
    }

    public function updateCustomer(int $id, array $data)
    {
        $customer = Customer::find($id);

        if (!$customer) {
            return null;
        }

        if (array_key_exists('name', $data)) {
            $customer->name = $data['name'];
        }

        if (array_key_exists('surname', $data)) {
            $customer->surname = $data['surname'];
        }

        $customer->save();

        return $customer;
    }

    public function deleteCustomer(int $id)
    {
        $customer = Customer::find($id);

        if ($customer) {
            $customer->delete();
            return true;
        }

        return false;
    }
}
