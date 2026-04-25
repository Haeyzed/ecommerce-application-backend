<?php

namespace App\Http\Requests\Tenant\HR;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDepartmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            /**
             * The updated name of the department.
             * @var string|null $name
             * @example "Software Engineering"
             */
            'name' => ['sometimes', 'string', 'max:255'],

            /**
             * The updated internal code for the department.
             * @var string|null $code
             * @example "SWE-01"
             */
            'code' => ['sometimes', 'nullable', 'string', 'max:32'],

            /**
             * The updated parent department ID.
             * @var int|null $parent_id
             * @example 2
             */
            'parent_id' => ['sometimes', 'nullable', 'integer', 'exists:departments,id'],

            /**
             * The updated ID of the employee managing this department.
             * @var int|null $manager_employee_id
             * @example 88
             */
            'manager_employee_id' => ['sometimes', 'nullable', 'integer', 'exists:employees,id'],
        ];
    }
}
