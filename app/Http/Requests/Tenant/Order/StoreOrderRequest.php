<?php

namespace App\Http\Requests\Tenant\Order;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property array $items The items being ordered.
 * @property array $shipping_address The shipping address details.
 */
class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items'                => ['required', 'array', 'min:1'],
            'items.*.product_id'   => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity'     => ['required', 'integer', 'min:1', 'max:99'],
            'shipping_address'     => ['required', 'array'],
        ];
    }
}
