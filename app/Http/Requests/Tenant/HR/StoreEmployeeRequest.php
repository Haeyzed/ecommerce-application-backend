<?php

namespace App\Http\Requests\Tenant\HR;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;

class StoreEmployeeRequest extends FormRequest
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
             * The ID of the linked staff user account. If omitted, a new Staff & User will be generated.
             *
             * @var int|null $staff_id
             *
             * @example 5
             */
            'staff_id' => ['nullable', 'integer', 'exists:staffs,id'],

            /**
             * The optional password to set for the newly auto-generated staff account.
             *
             * @var string|null $password
             *
             * @example "SecretP@ssw0rd!"
             */
            'password' => ['nullable', 'string', 'min:8'],

            /**
             * The ID of the department the employee belongs to.
             *
             * @var int|null $department_id
             *
             * @example 2
             */
            'department_id' => ['nullable', 'integer', 'exists:departments,id'],

            /**
             * The ID of the position the employee holds.
             *
             * @var int|null $position_id
             *
             * @example 10
             */
            'position_id' => ['nullable', 'integer', 'exists:positions,id'],

            /**
             * The employee's first name.
             *
             * @var string $first_name
             *
             * @example "John"
             */
            'first_name' => ['required', 'string', 'max:128'],

            /**
             * The employee's last name.
             *
             * @var string $last_name
             *
             * @example "Doe"
             */
            'last_name' => ['required', 'string', 'max:128'],

            /**
             * The employee's email address.
             *
             * @var string $email
             *
             * @example "john.doe@company.com"
             */
            'email' => ['required', 'email', 'max:255', 'unique:employees,email'],

            /**
             * The employee's phone number.
             *
             * @var string|null $phone
             *
             * @example "+1987654321"
             */
            'phone' => ['nullable', 'string', 'max:32'],

            /**
             * The type of employment.
             *
             * @var string|null $employment_type
             *
             * @example "full_time"
             */
            'employment_type' => ['nullable', 'in:full_time,part_time,contract,intern'],

            /**
             * The employee's salary in minor units (cents).
             *
             * @var int|null $salary_cents
             *
             * @example 7500000
             */
            'salary_cents' => ['nullable', 'integer', 'min:0'],

            /**
             * The ISO currency code for the salary.
             *
             * @var string|null $currency
             *
             * @example "USD"
             */
            'currency' => ['nullable', 'string', 'size:3'],

            /**
             * The date the employee was hired.
             *
             * @var string|null $hired_at
             *
             * @example "2026-01-15"
             */
            'hired_at' => ['nullable', 'date'],

            /**
             * Indicates if the employee is currently active.
             *
             * @var bool|null $is_active
             *
             * @example true
             */
            'is_active' => ['boolean'],

            /**
             * The employee's avatar image file.
             *
             * @var UploadedFile|null $avatar
             */
            'avatar' => ['nullable', 'file', 'image', 'max:2048'],
        ];
    }
}
