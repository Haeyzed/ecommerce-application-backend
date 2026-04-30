<?php

namespace App\Http\Requests\Tenant\Review;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property int $product_id The ID of the product being reviewed.
 * @property int $rating The rating given to the product (1-5).
 * @property string|null $title The title of the review.
 * @property string $body The main text body of the review.
 */
class StoreReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'title' => ['nullable', 'string', 'max:120'],
            'body' => ['required', 'string', 'max:2000'],
        ];
    }
}
