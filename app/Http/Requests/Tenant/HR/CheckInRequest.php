<?php

namespace App\Http\Requests\Tenant\HR;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CheckInRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            /**
             * The ID of the employee checking in.
             *
             * @var int $employee_id
             *
             * @example 42
             */
            'employee_id' => ['required', 'integer', 'exists:employees,id'],

            /**
             * The explicit timestamp for the check-in event.
             *
             * @var string|null $at
             *
             * @example "2026-04-25 09:00:00"
             */
            'at' => ['nullable', 'date'],
        ];
    }
}
