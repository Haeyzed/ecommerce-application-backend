<?php

namespace App\Http\Requests\Tenant\HR;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ScheduleInterviewRequest extends FormRequest
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
             * The ID of the applicant to be interviewed.
             *
             * @var int $applicant_id
             *
             * @example 8
             */
            'applicant_id' => ['required', 'integer', 'exists:applicants,id'],

            /**
             * The ID of the employee conducting the interview.
             *
             * @var int|null $interviewer_employee_id
             *
             * @example 12
             */
            'interviewer_employee_id' => ['nullable', 'integer', 'exists:employees,id'],

            /**
             * The date and time the interview is scheduled.
             *
             * @var string $scheduled_at
             *
             * @example "2026-05-10 14:00:00"
             */
            'scheduled_at' => ['required', 'date'],

            /**
             * The mode of the interview (onsite, video, phone).
             *
             * @var string|null $mode
             *
             * @example "video"
             */
            'mode' => ['nullable', 'in:onsite,video,phone'],

            /**
             * Additional notes for the interview.
             *
             * @var string|null $notes
             *
             * @example "Please bring a copy of your portfolio."
             */
            'notes' => ['nullable', 'string'],
        ];
    }
}
