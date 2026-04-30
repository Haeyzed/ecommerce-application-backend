<?php

namespace App\Http\Requests\Tenant\HR;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePositionRequest extends FormRequest
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
             * The ID of the department this position belongs to.
             *
             * @var int $department_id
             *
             * @example 3
             */
            'department_id' => ['required', 'integer', 'exists:departments,id'],

            /**
             * The job title for the position.
             *
             * @var string $title
             *
             * @example "Senior Frontend Developer"
             */
            'title' => ['required', 'string', 'max:255'],

            /**
             * The minimum salary boundary in minor units (cents).
             *
             * @var int|null $min_salary_cents
             *
             * @example 7000000
             */
            'min_salary_cents' => ['nullable', 'integer', 'min:0'],

            /**
             * The maximum salary boundary in minor units (cents).
             *
             * @var int|null $max_salary_cents
             *
             * @example 12000000
             */
            'max_salary_cents' => ['nullable', 'integer', 'min:0', 'gte:min_salary_cents'],
        ];
    }
}
