<?php

namespace App\Http\Requests\Central\Plan;

use App\Models\Central\Plan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property string $name The display name of the plan.
 * @property string|null $slug Optional slug; generated from name when omitted.
 * @property string|null $description Plan marketing or internal description.
 * @property int $price_cents The price in minor units (e.g. cents).
 * @property string|null $currency ISO 4217 code (default USD).
 * @property string|null $interval Billing cadence (e.g. month, year).
 * @property array|null $features Feature list for display or gating.
 * @property array $limits Resource caps (e.g. max_products, allows_custom_domain).
 * @property bool|null $is_active Whether the plan can be sold.
 */
class StorePlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['sometimes', 'nullable', 'string', 'max:255', Rule::unique(Plan::class, 'slug')],
            'description' => ['nullable', 'string'],
            'price_cents' => ['required', 'integer', 'min:0'],
            'currency' => ['sometimes', 'string', 'size:3'],
            'interval' => ['nullable', 'string', 'max:32'],
            'features' => ['nullable', 'array'],
            'features.*' => ['string', 'max:255'],
            'limits' => ['required', 'array'],
            'limits.max_products' => ['required', 'integer', 'min:1'],
            'limits.allows_custom_domain' => ['required', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
