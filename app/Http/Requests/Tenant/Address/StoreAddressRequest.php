<?php

namespace App\Http\Requests\Tenant\Address;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string $type The type of address (shipping or billing).
 * @property string $name The name associated with the address.
 * @property string $line1 Address line 1.
 * @property string|null $line2 Address line 2.
 * @property string $city The city.
 * @property string|null $state The state or province.
 * @property string $postal_code The postal or ZIP code.
 * @property string $country The 2-letter country code.
 * @property string|null $phone A contact phone number.
 * @property bool|null $is_default Indicates if this is the default address.
 */
class StoreAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'in:shipping,billing'],
            'name' => ['required', 'string'],
            'line1' => ['required', 'string'],
            'line2' => ['nullable', 'string'],
            'city' => ['required', 'string'],
            'state' => ['nullable', 'string'],
            'postal_code' => ['required', 'string'],
            'country' => ['required', 'string', 'size:2'],
            'phone' => ['nullable', 'string'],
            'is_default' => ['boolean'],
        ];
    }
}
