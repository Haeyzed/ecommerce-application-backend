<?php

namespace App\Http\Requests\Tenant\HR;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePerformanceReviewRequest extends FormRequest
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
             * The ID of the employee being reviewed.
             *
             * @var int $employee_id
             *
             * @example 5
             */
            'employee_id' => ['required', 'integer', 'exists:employees,id'],

            /**
             * The ID of the employee conducting the review.
             *
             * @var int|null $reviewer_employee_id
             *
             * @example 2
             */
            'reviewer_employee_id' => ['nullable', 'integer', 'exists:employees,id'],

            /**
             * The start date of the evaluation period.
             *
             * @var string $period_start
             *
             * @example "2026-01-01"
             */
            'period_start' => ['required', 'date'],

            /**
             * The end date of the evaluation period.
             *
             * @var string $period_end
             *
             * @example "2026-03-31"
             */
            'period_end' => ['required', 'date', 'after_or_equal:period_start'],

            /**
             * The overall performance rating.
             *
             * @var float|null $rating
             *
             * @example 4.5
             */
            'rating' => ['nullable', 'numeric', 'min:0', 'max:5'],

            /**
             * A JSON array containing scores for specific criteria.
             *
             * @var array|null $criteria
             *
             * @example {"teamwork": 4, "communication": 5}
             */
            'criteria' => ['nullable', 'array'],

            /**
             * Additional notes or feedback from the reviewer.
             *
             * @var string|null $comments
             *
             * @example "Excellent performance during Q1."
             */
            'comments' => ['nullable', 'string'],
        ];
    }
}
