<?php

namespace App\Http\Requests\Tenant\HR;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class GeneratePayslipRequest extends FormRequest
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
             * The ID of the employee receiving the payslip.
             *
             * @var int $employee_id
             *
             * @example 105
             */
            'employee_id' => ['required', 'integer', 'exists:employees,id'],

            /**
             * The start date of the pay period.
             *
             * @var string $period_start
             *
             * @example "2026-04-01"
             */
            'period_start' => ['required', 'date'],

            /**
             * The end date of the pay period.
             *
             * @var string $period_end
             *
             * @example "2026-04-30"
             */
            'period_end' => ['required', 'date', 'after_or_equal:period_start'],

            /**
             * A breakdown array of deductions applied to the payslip.
             *
             * @var array|null $deductions
             *
             * @example [{"label": "Health Insurance", "amount_cents": 5000}]
             */
            'deductions' => ['nullable', 'array'],

            /**
             * The label for a specific deduction.
             *
             * @var string $deductions .*.label
             *
             * @example "Health Insurance"
             */
            'deductions.*.label' => ['required_with:deductions', 'string', 'max:128'],

            /**
             * The amount of the deduction in cents.
             *
             * @var int $deductions .*.amount_cents
             *
             * @example 5000
             */
            'deductions.*.amount_cents' => ['required_with:deductions', 'integer', 'min:0'],
        ];
    }
}
