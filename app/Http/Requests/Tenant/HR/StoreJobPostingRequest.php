<?php

namespace App\Http\Requests\Tenant\HR;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreJobPostingRequest extends FormRequest
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
             * The ID of the department advertising the position.
             *
             * @var int|null $department_id
             *
             * @example 4
             */
            'department_id' => ['nullable', 'integer', 'exists:departments,id'],

            /**
             * The job title for the posting.
             *
             * @var string $title
             *
             * @example "Senior Backend Engineer"
             */
            'title' => ['required', 'string', 'max:255'],

            /**
             * The detailed description of the job posting.
             *
             * @var string $description
             *
             * @example "We are looking for a Senior Backend Engineer to join our team..."
             */
            'description' => ['required', 'string'],

            /**
             * The type of employment (e.g., full_time, part_time).
             *
             * @var string|null $employment_type
             *
             * @example "full_time"
             */
            'employment_type' => ['nullable', 'in:full_time,part_time,contract,intern'],

            /**
             * The physical or remote location of the job.
             *
             * @var string|null $location
             *
             * @example "Remote"
             */
            'location' => ['nullable', 'string', 'max:128'],

            /**
             * Indicates whether the job posting is currently open/active.
             *
             * @var bool|null $is_open
             *
             * @example true
             */
            'is_open' => ['boolean'],

            /**
             * The date and time when the job posting automatically closes.
             *
             * @var string|null $closes_at
             *
             * @example "2026-06-30 23:59:59"
             */
            'closes_at' => ['nullable', 'date'],
        ];
    }
}
