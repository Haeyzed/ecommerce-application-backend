<?php

namespace App\Http\Controllers\Tenant\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Address\StoreAddressRequest;
use App\Models\Tenant\Address;
use App\Services\Tenant\AddressService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

/**
 * Address Endpoints
 * * Handles the creation, retrieval, and deletion of customer addresses.
 */
class AddressController extends Controller
{
    /**
     * Create a new AddressController instance.
     *
     * @param AddressService $addressService
     */
    public function __construct(
        private readonly AddressService $addressService
    ) {}

    /**
     * List customer addresses.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $addresses = $this->addressService->getCustomerAddresses($request->user()->id);

        return ApiResponse::success(
            ['addresses' => $addresses],
            'Addresses retrieved successfully'
        );
    }

    /**
     * Create a new address.
     *
     * @param StoreAddressRequest $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function store(StoreAddressRequest $request): JsonResponse
    {
        $address = $this->addressService->createAddress(
            $request->user()->id,
            $request->validated()
        );

        return ApiResponse::success(
            ['address' => $address],
            'Address created successfully',
            null,
            201
        );
    }

    /**
     * Delete an address.
     *
     * @param Address $address
     * @return JsonResponse
     */
    public function destroy(Address $address): JsonResponse
    {
        $this->addressService->deleteAddress($address);

        return ApiResponse::success(null, 'Address deleted successfully');
    }
}
