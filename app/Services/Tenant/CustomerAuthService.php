<?php

namespace App\Services\Tenant;

use App\Models\Tenant\Customer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * Class CustomerAuthService
 * * Handles authentication logic for storefront customers.
 */
class CustomerAuthService
{
    /**
     * Register a new customer.
     *
     * @param array $data
     * @return array Contains 'customer' and 'token'.
     */
    public function register(array $data): array
    {
        $customer = Customer::query()->create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        return [
            'customer' => $customer,
            'token'    => $customer->createToken('storefront')->plainTextToken,
        ];
    }

    /**
     * Authenticate an existing customer.
     *
     * @param array $credentials
     * @return array Contains 'customer' and 'token'.
     * @throws ValidationException
     */
    public function login(array $credentials): array
    {
        $customer = Customer::query()->where('email', $credentials['email'])->first();

        if (! $customer || ! Hash::check($credentials['password'], $customer->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials provided.'],
            ]);
        }

        return [
            'customer' => $customer,
            'token'    => $customer->createToken('storefront')->plainTextToken,
        ];
    }

    /**
     * Logout a customer by revoking their current token.
     *
     * @param Customer $customer
     * @return void
     */
    public function logout(Customer $customer): void
    {
        $customer->currentAccessToken()->delete();
    }
}
