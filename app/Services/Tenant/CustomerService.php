<?php

namespace App\Services\Tenant;

use App\Models\Tenant\Customer;
use App\Models\Tenant\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Throwable;

/**
 * Class CustomerService
 * * Handles CRUD business logic related to tenant customer profiles.
 * * Automatically manages the underlying User account relationship.
 */
class CustomerService
{
    /**
     * Retrieve a paginated, filtered list of customers.
     *
     * @param  array  $filters  Query filters (e.g., search)
     * @param  int  $perPage  Items per page
     */
    public function getPaginatedCustomers(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return Customer::query()
            ->with('user')
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->whereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })->orWhere('phone', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Retrieve a specific customer by their ID.
     */
    public function getCustomerById(int $id): Customer
    {
        return Customer::query()
            ->with('user')
            ->findOrFail($id);
    }

    /**
     * Create a new customer profile and underlying user account.
     *
     * @param  array  $data  Validated customer data.
     *
     * @throws Throwable
     */
    public function createCustomer(array $data): Customer
    {
        return DB::transaction(function () use ($data) {
            $user = User::query()->firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'user_type' => 'customer',
                    'password' => Hash::make($data['password']),
                ]
            );

            $profileData = Arr::only($data, [
                'phone', 'avatar_url', 'date_of_birth', 'gender',
                'currency', 'locale', 'notes', 'is_active',
            ]);

            $customer = Customer::query()->updateOrCreate(
                ['user_id' => $user->id],
                $profileData
            );

            return $customer->load('user');
        });
    }

    /**
     * Update an existing customer's underlying user account and storefront profile details.
     *
     * @param  array  $data  Validated update data.
     *
     * @throws Throwable
     */
    public function updateCustomer(Customer $customer, array $data): Customer
    {
        return DB::transaction(function () use ($customer, $data) {
            $user = $customer->user;

            if (isset($data['name'])) {
                $user->name = $data['name'];
            }
            if (isset($data['email'])) {
                $user->email = $data['email'];
            }
            if (! empty($data['password'])) {
                $user->password = Hash::make($data['password']);
            }

            if ($user->isDirty()) {
                $user->save();
            }

            $profileData = Arr::only($data, [
                'phone', 'avatar_url', 'date_of_birth', 'gender',
                'currency', 'locale', 'notes', 'is_active',
            ]);

            if (! empty($profileData)) {
                $customer->update($profileData);
            }

            return $customer->fresh('user');
        });
    }

    /**
     * Soft delete a customer profile.
     */
    public function deleteCustomer(Customer $customer): void
    {
        $customer->delete();
    }
}
