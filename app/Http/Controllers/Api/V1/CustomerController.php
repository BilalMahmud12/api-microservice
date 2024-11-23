<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\CustomerService;

class CustomerController extends Controller
{
    protected $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    public function getAllCustomers(Request $request)
    {
        return response()->json('working', 200);
    }

    public function createCustomer(Request $request)
    {
        //
    }

    public function getCustomerById($id)
    {
        //
    }

    public function updateCustomer(Request $request, $id)
    {
        //
    }

    public function deleteCustomer($id)
    {
        //
    }
}
