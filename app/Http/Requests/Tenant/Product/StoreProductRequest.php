<?php

namespace App\Http\Requests\Tenant\Product;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string $name The name of the product.
 * @property string|null $description The product description.
 * @property int $price_cents The product price in cents.
 * @property string|null $currency The 3-letter currency code.
 * @property int|null $stock The initial stock quantity.
 * @property int|null $category_id The ID of the category.
 * @property string|null $image_url The product image URL.
 * @property bool|null $is_active Whether the product is active.
 */
class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'         => ['required', 'string', 'max:160'],
            'description'  => ['nullable', 'string'],
            'price_cents'  => ['required', 'integer', 'min:0'],
            'currency'     => ['nullable', 'string', 'size:3'],
            'stock'        => ['nullable', 'integer', 'min:0'],
            'category_id'  => ['nullable', 'integer', 'exists:categories,id'],
            'image_url'    => ['nullable', 'url'],
            'is_active'    => ['boolean'],
        ];
    }
}
