<?php

namespace App\Http\Requests\Tenant\Wishlist;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property int $product_id The ID of the product to toggle on the wishlist.
 */
class ToggleWishlistRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer'],
        ];
    }
}
