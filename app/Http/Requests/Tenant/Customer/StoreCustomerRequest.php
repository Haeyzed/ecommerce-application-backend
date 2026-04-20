<?php

namespace App\Http\Requests\Tenant\Customer;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string $name The full name of the customer.
 * @property string $email The email address of the customer.
 * @property string $password The initial password for the customer account.
 * @property string|null $phone The customer's contact phone number.
 * @property string|null $avatar_url The URL to the customer's profile picture.
 * @property string|null $date_of_birth The customer's date of birth.
 * @property string|null $gender The customer's gender.
 * @property string|null $currency The customer's preferred currency code.
 * @property string|null $locale The customer's preferred language/locale.
 * @property string|null $notes Internal notes about the customer.
 * @property bool|null $is_active Indicates if the customer profile is active.
 */
class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // User Auth Fields
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'email'],
            'password'      => ['required', 'string', 'min:8'],

            // Customer Profile Fields
            'phone'         => ['nullable', 'string', 'max:50'],
            'avatar_url'    => ['nullable', 'url'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'gender'        => ['nullable', 'string', 'max:20'],
            'currency'      => ['nullable', 'string', 'size:3'],
            'locale'        => ['nullable', 'string', 'max:5'],
            'notes'         => ['nullable', 'string'],
            'is_active'     => ['boolean'],
        ];
    }
}
