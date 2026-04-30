<?php

namespace App\Http\Requests\Central\Tenant;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string|null $name The updated name of the tenant's store. @example Acme Corporation
 * @property int|null $plan_id The updated ID of the subscription plan. @example 2
 */
class UpdateTenantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:120'],
            'plan_id' => ['sometimes', 'nullable', 'integer', 'exists:plans,id'],
        ];
    }
}
