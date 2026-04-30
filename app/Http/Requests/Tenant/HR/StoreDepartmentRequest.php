<?php

namespace App\Http\Requests\Tenant\HR;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string $name
 * @property string|null $code
 * @property int|null $parent_id
 * @property int|null $manager_employee_id
 */
class StoreDepartmentRequest extends FormRequest
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
             * The name of the department.
             *
             * @var string $name
             *
             * @example "Engineering"
             */
            'name' => ['required', 'string', 'max:255'],

            /**
             * An internal code for the department.
             *
             * @var string|null $code
             *
             * @example "ENG-01"
             */
            'code' => ['nullable', 'string', 'max:32'],

            /**
             * The ID of the parent department, if applicable.
             *
             * @var int|null $parent_id
             *
             * @example 1
             */
            'parent_id' => ['nullable', 'integer', 'exists:departments,id'],

            /**
             * The ID of the employee who manages this department.
             *
             * @var int|null $manager_employee_id
             *
             * @example 45
             */
            'manager_employee_id' => ['nullable', 'integer', 'exists:employees,id'],
        ];
    }
}
