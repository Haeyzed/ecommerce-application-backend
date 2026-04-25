<?php

namespace App\Http\Requests\Central\Plan;

use App\Models\Central\Plan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property string|null $name The display name of the plan.
 * @property string|null $slug URL-friendly identifier.
 * @property string|null $description Plan description.
 * @property int|null $price_cents The price in minor units.
 * @property string|null $currency ISO 4217 code.
 * @property string|null $interval Billing cadence.
 * @property array|null $features Feature list.
 * @property array|null $limits Resource caps.
 * @property bool|null $is_active Whether the plan is available.
 */
class UpdatePlanRequest extends FormRequest
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
            'name' => ['sometimes', 'string', 'max:255'],
            'slug' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
                Rule::unique(Plan::class, 'slug')->ignore($this->route('plan')),
            ],
            'description' => ['sometimes', 'nullable', 'string'],
            'price_cents' => ['sometimes', 'integer', 'min:0'],
            'currency' => ['sometimes', 'string', 'size:3'],
            'interval' => ['sometimes', 'nullable', 'string', 'max:32'],
            'features' => ['sometimes', 'nullable', 'array'],
            'features.*' => ['string', 'max:255'],
            'limits' => ['sometimes', 'array'],
            'limits.max_products' => ['sometimes', 'integer', 'min:1'],
            'limits.allows_custom_domain' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
