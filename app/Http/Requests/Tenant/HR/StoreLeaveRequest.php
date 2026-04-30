<?php

namespace App\Http\Requests\Tenant\HR;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreLeaveRequest extends FormRequest
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
             * The ID of the employee requesting leave.
             *
             * @var int $employee_id
             *
             * @example 12
             */
            'employee_id' => ['required', 'integer', 'exists:employees,id'],

            /**
             * The type of leave (sick, vacation, maternity, unpaid, other).
             *
             * @var string $type
             *
             * @example "vacation"
             */
            'type' => ['required', 'in:sick,vacation,maternity,unpaid,other'],

            /**
             * The start date of the leave.
             *
             * @var string $start_date
             *
             * @example "2026-05-15"
             */
            'start_date' => ['required', 'date'],

            /**
             * The end date of the leave.
             *
             * @var string $end_date
             *
             * @example "2026-05-20"
             */
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],

            /**
             * The reason for the leave request.
             *
             * @var string|null $reason
             *
             * @example "Annual family trip."
             */
            'reason' => ['nullable', 'string'],
        ];
    }
}
