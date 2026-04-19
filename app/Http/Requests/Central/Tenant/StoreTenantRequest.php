<?php

namespace App\Http\Requests\Central\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property string $name The name of the tenant's store. @example Acme Corp
 * @property string $subdomain The desired default subdomain. @example acme
 * @property string $owner_email The email of the store owner. @example owner@acme.com
 * @property int|null $plan_id The ID of the subscription plan. @example 1
 */
class StoreTenantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:120'],
            'subdomain'   => [
                'required', 'string', 'alpha_dash', 'min:2', 'max:40',
                Rule::notIn(['www', 'api', 'admin', 'app', 'mail'])
            ],
            'owner_email' => ['required', 'email'],
            'plan_id'     => ['nullable', 'integer', 'exists:plans,id'],
        ];
    }
}
