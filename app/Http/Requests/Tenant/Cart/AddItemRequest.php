<?php

namespace App\Http\Requests\Tenant\Cart;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property int $product_id The ID of the product being added to the cart.
 * @property int $quantity The quantity of the product to add.
 */
class AddItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity'   => ['required', 'integer', 'min:1', 'max:99'],
        ];
    }
}
