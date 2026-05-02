<?php

namespace App\Http\Requests\Central\Subscription;

use Illuminate\Foundation\Http\FormRequest;

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
