<?php

namespace App\Services\Tenant;

use App\Models\Tenant\Address;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Class AddressService
 * * Handles business logic related to tenant customer addresses.
 */
class AddressService
{
    /**
     * Retrieve all addresses for a specific customer.
     *
     * @param int $customerId
     * @return Collection
     */
    public function getCustomerAddresses(int $customerId): Collection
    {
        return Address::query()
            ->where('customer_id', $customerId)
            ->get();
    }

    /**
     * Create a new address and manage default address status.
     *
     * @param int $customerId
     * @param array $data Validated address data.
     * @return Address
     * @throws Throwable
     */
    public function createAddress(int $customerId, array $data): Address
    {
        return DB::transaction(function () use ($customerId, $data) {
            $data['customer_id'] = $customerId;

            if (!empty($data['is_default'])) {
                Address::query()
                    ->where('customer_id', $customerId)
                    ->where('type', $data['type'] ?? 'shipping')
                    ->update(['is_default' => false]);
            }

            return Address::query()->create($data);
        });
    }

    /**
     * Delete an address.
     *
     * @param Address $address
     * @return void
     */
    public function deleteAddress(Address $address): void
    {
        $address->delete();
    }
}
