<?php

namespace App\Http\Requests\Tenant\Cart;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property int $quantity The new quantity for the cart item.
 */
class UpdateItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'quantity' => ['required', 'integer', 'min:0', 'max:99'],
        ];
    }
}
