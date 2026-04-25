<?php

namespace App\Http\Resources\Tenant\HR;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PerformanceReviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            /**
             * The unique identifier for the performance review.
             * @var int $id
             * @example 12
             */
            'id'                   => $this->id,

            /**
             * The ID of the employee being reviewed.
             * @var int $employee_id
             * @example 42
             */
            'employee_id'          => $this->employee_id,

            /**
             * The ID of the employee conducting the review.
             * @var int|null $reviewer_employee_id
             * @example 10
             */
            'reviewer_employee_id' => $this->reviewer_employee_id,

            /**
             * The start date of the evaluation period.
             * @var string $period_start
             * @example "2026-01-01"
             */
            'period_start'         => $this->period_start,

            /**
             * The end date of the evaluation period.
             * @var string $period_end
             * @example "2026-03-31"
             */
            'period_end'           => $this->period_end,

            /**
             * The overall performance rating.
             * @var float|null $rating
             * @example 4.5
             */
            'rating'               => $this->rating,

            /**
             * A JSON array containing scores for specific criteria.
             * @var array|null $criteria
             * @example {"teamwork": 4, "communication": 5}
             */
            'criteria'             => $this->criteria,

            /**
             * Additional notes or feedback from the reviewer.
             * @var string|null $comments
             * @example "Excellent performance during Q1."
             */
            'comments'             => $this->comments,

            /**
             * The status of the review (e.g., draft, completed).
             * @var string $status
             * @example "completed"
             */
            'status'               => $this->status,
        ];
    }
}
