<?php

namespace App\Http\Requests\Tenant\Customer;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string|null $name The updated full name.
 * @property string|null $email The updated email address.
 * @property string|null $password A new password (if changing).
 * @property string|null $phone The customer's contact phone number.
 * @property string|null $avatar_url The URL to the customer's profile picture.
 * @property string|null $date_of_birth The customer's date of birth.
 * @property string|null $gender The customer's gender.
 * @property string|null $currency The customer's preferred currency code.
 * @property string|null $locale The customer's preferred language/locale.
 * @property string|null $notes Internal notes about the customer.
 * @property bool|null $is_active Indicates if the customer profile is active.
 */
class UpdateCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // User Auth Fields
            'name'          => ['sometimes', 'string', 'max:255'],
            'email'         => ['sometimes', 'email'],
            'password'      => ['sometimes', 'nullable', 'string', 'min:8'],

            // Customer Profile Fields
            'phone'         => ['sometimes', 'nullable', 'string', 'max:50'],
            'avatar_url'    => ['sometimes', 'nullable', 'url'],
            'date_of_birth' => ['sometimes', 'nullable', 'date', 'before:today'],
            'gender'        => ['sometimes', 'nullable', 'string', 'max:20'],
            'currency'      => ['sometimes', 'nullable', 'string', 'size:3'],
            'locale'        => ['sometimes', 'nullable', 'string', 'max:5'],
            'notes'         => ['sometimes', 'nullable', 'string'],
            'is_active'     => ['sometimes', 'boolean'],
        ];
    }
}
