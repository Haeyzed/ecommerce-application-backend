<?php

namespace App\Http\Requests\Central\Plan;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string $name The display name of the plan. @example Pro Plan
 * @property string $slug The URL-friendly identifier. @example pro-plan
 * @property int $price_cents The price in cents. @example 2999
 * @property int $max_products The maximum products allowed. @example 1000
 * @property bool $allows_custom_domain Whether custom domains are allowed. @example true
 */
class StorePlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'                 => ['required', 'string', 'max:255'],
            'slug'                 => ['required', 'string', 'max:255', 'unique:plans,slug'],
            'price_cents'          => ['required', 'integer', 'min:0'],
            'max_products'         => ['required', 'integer', 'min:1'],
            'allows_custom_domain' => ['required', 'boolean'],
        ];
    }
}
