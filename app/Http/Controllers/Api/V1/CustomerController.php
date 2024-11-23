<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;
use App\Services\CustomerService;
use App\Services\Validators\CustomerValidator;

class CustomerController extends Controller
{
    protected $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    public function getAllCustomers(Request $request)
    {
        Log::info('Start getting all customers.');

        try {
            $page = (int) $request->query('page', 1);
            $perPage = (int) $request->query('per_page', 10);

            $validationResult = CustomerValidator::validatePaginationParams($page, $perPage);

            if ($validationResult !== true) {
                return $validationResult;
            }

            $customers = $this->customerService->getAllCustomers($page, $perPage);

            Log::info('Customers retrieved successfully. Count: ' . count($customers));
            return response()->json($customers, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve customers: ' . $e->getMessage());
            return response()->json(
                ['error' => 'Failed to retrieve customers', 'details' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function createCustomer(Request $request)
    {
        Log::info('Start creating a new customer.');

        try {
            if (empty($request->all())) {
                Log::warning('Empty create request received.');
                return response()->json(
                    ['error' => 'Empty create request received, name and surname are required'],
                    Response::HTTP_BAD_REQUEST
                );
            }

            $validationResult = CustomerValidator::validateCreateCustomer($request);

            if ($validationResult !== true) {
                return $validationResult;
            }

            $customer = $this->customerService->createCustomer($request->only(['name', 'surname']));

            Log::info('Customer created successfully with ID: ' . $customer->id . ', Data: ' . json_encode($customer));
            return response()->json($customer, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            Log::error('Failed to create customer: ' . $e->getMessage());
            return response()->json(
                ['error' => 'Failed to create customer', 'details' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function getCustomerById(string $id)
    {
        Log::info('Start getting customer by ID: ' . $id);

        try {
            $customer = $this->customerService->getCustomerById($id);

            if (!$customer) {
                Log::error('Customer not found for ID: ' . $id);
                return response()->json(
                    ['error' => 'Customer not found'],
                    Response::HTTP_NOT_FOUND
                );
            }

            Log::info('Customer retrieved successfully with ID: ' . $id . ', Data: ' . json_encode($customer));
            return response()->json($customer, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve customer: ' . $e->getMessage());
            return response()->json(
                ['error' => 'Failed to retrieve customer', 'details' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function updateCustomer(Request $request, string $id)
    {
        Log::info('Start updating customer with ID: ' . $id);

        try {
            if (empty($request->all())) {
                Log::warning('Empty update request received for customer ID: ' . $id);
                return response()->json(
                    ['error' => 'Empty update request received, At least one field must be provided for update'],
                    Response::HTTP_BAD_REQUEST
                );
            }

            $validationResult = CustomerValidator::validateUpdateCustomer($request);

            if ($validationResult !== true) {
                return $validationResult;
            }

            $customer = $this->customerService->updateCustomer($id, $request->only(['name', 'surname']));

            if (!$customer) {
                Log::error('Customer not found for ID: ' . $id);
                return response()->json(
                    ['error' => 'Customer not found'],
                    Response::HTTP_NOT_FOUND
                );
            }

            Log::info('Customer updated successfully with ID: ' . $id . ', Data: ' . json_encode($customer));
            return response()->json($customer, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to update customer: ' . $e->getMessage());
            return response()->json(
                ['error' => 'Failed to update customer', 'details' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function deleteCustomer(string $id)
    {
        Log::info('Start deleting customer with ID: ' . $id);

        try {
            $success = $this->customerService->deleteCustomer($id);

            if (!$success) {
                Log::error('Customer not found for ID: ' . $id);
                return response()->json(
                    ['error' => 'Customer not found'],
                    Response::HTTP_NOT_FOUND
                );
            }

            Log::info('Customer deleted successfully with ID: ' . $id);
            return response()->json(
                ['success' => $success],
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::error('Failed to delete customer: ' . $e->getMessage());
            return response()->json(
                ['error' => 'Failed to delete customer', 'details' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
