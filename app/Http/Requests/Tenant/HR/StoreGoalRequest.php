<?php

namespace App\Http\Requests\Tenant\HR;

use Illuminate\Foundation\Http\FormRequest;

class StoreGoalRequest extends FormRequest
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
             * The ID of the employee this goal is assigned to.
             * @var int $employee_id
             * @example 34
             */
            'employee_id' => ['required', 'integer', 'exists:employees,id'],

            /**
             * The title or name of the goal.
             * @var string $title
             * @example "Complete AWS Certification"
             */
            'title' => ['required', 'string', 'max:255'],

            /**
             * A detailed description of the goal.
             * @var string|null $description
             * @example "Study for and pass the AWS Certified Solutions Architect exam."
             */
            'description' => ['nullable', 'string'],

            /**
             * The target completion date for the goal.
             * @var string|null $target_date
             * @example "2026-08-31"
             */
            'target_date' => ['nullable', 'date'],

            /**
             * The current progress percentage (0-100).
             * @var int|null $progress_percent
             * @example 50
             */
            'progress_percent' => ['nullable', 'integer', 'min:0', 'max:100'],

            /**
             * The current status of the goal.
             * @var string|null $status
             * @example "in_progress"
             */
            'status' => ['nullable', 'in:open,in_progress,completed,missed'],
        ];
    }
}
