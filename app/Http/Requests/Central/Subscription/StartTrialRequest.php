<?php

namespace App\Http\Requests\Central\Subscription;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string $tenant_id The ID of the tenant.
 * @property int $plan_id The ID of the plan.
 * @property int|null $trial_days The number of trial days.
 */
class StartTrialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tenant_id' => ['required', 'string', 'exists:tenants,id'],
            'plan_id' => ['required', 'integer', 'exists:plans,id'],
            'trial_days' => ['nullable', 'integer', 'min:1', 'max:90'],
        ];
    }
}
