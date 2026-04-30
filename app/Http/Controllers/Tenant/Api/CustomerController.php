<?php

namespace App\Http\Controllers\Tenant\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Customer\StoreCustomerRequest;
use App\Http\Requests\Tenant\Customer\UpdateCustomerRequest;
use App\Models\Tenant\Customer;
use App\Services\Tenant\CustomerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

/**
 * Customer Endpoints
 * * Handles the CRUD operations (creation, retrieval, updating, and deletion) of customer profiles.
 */
class CustomerController extends Controller
{
    /**
     * Create a new CustomerController instance.
     */
    public function __construct(
        private readonly CustomerService $customerService
    ) {}

    /**
     * List all customers.
     */
    public function index(Request $request): JsonResponse
    {
        $filters = [
            'search' => $request->query('q'),
        ];

        $customers = $this->customerService->getPaginatedCustomers($filters);

        return ApiResponse::success(
            ['customers' => $customers],
            'Customers retrieved successfully'
        );
    }

    /**
     * Get specific customer details.
     */
    public function show(int $id): JsonResponse
    {
        $customer = $this->customerService->getCustomerById($id);

        return ApiResponse::success(
            ['customer' => $customer],
            'Customer retrieved successfully'
        );
    }

    /**
     * Create a new customer manually.
     *
     * @throws Throwable
     */
    public function store(StoreCustomerRequest $request): JsonResponse
    {
        $customer = $this->customerService->createCustomer($request->validated());

        return ApiResponse::success(
            ['customer' => $customer],
            'Customer created successfully',
            null,
            201
        );
    }

    /**
     * Update an existing customer.
     *
     * @throws Throwable
     */
    public function update(UpdateCustomerRequest $request, int $id): JsonResponse
    {
        $customer = Customer::query()->findOrFail($id);

        $updatedCustomer = $this->customerService->updateCustomer($customer, $request->validated());

        return ApiResponse::success(
            ['customer' => $updatedCustomer],
            'Customer updated successfully'
        );
    }

    /**
     * Delete a customer.
     */
    public function destroy(int $id): JsonResponse
    {
        $customer = Customer::query()->findOrFail($id);

        $this->customerService->deleteCustomer($customer);

        return ApiResponse::success(null, 'Customer deleted successfully');
    }
}
