<?php

namespace App\Http\Resources\Tenant\HR;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GoalResource extends JsonResource
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
             * The unique identifier for the goal.
             * @var int $id
             * @example 5
             */
            'id'               => $this->id,

            /**
             * The ID of the employee this goal is assigned to.
             * @var int $employee_id
             * @example 42
             */
            'employee_id'      => $this->employee_id,

            /**
             * The title of the goal.
             * @var string $title
             * @example "Complete AWS Certification"
             */
            'title'            => $this->title,

            /**
             * A detailed description of the goal.
             * @var string|null $description
             * @example "Study and pass the AWS Certified Solutions Architect exam."
             */
            'description'      => $this->description,

            /**
             * The target completion date for the goal.
             * @var string|null $target_date
             * @example "2026-08-31"
             */
            'target_date'      => $this->target_date,

            /**
             * The current progress percentage towards the goal (0-100).
             * @var int|null $progress_percent
             * @example 50
             */
            'progress_percent' => $this->progress_percent,

            /**
             * The current status of the goal.
             * @var string $status
             * @example "in_progress"
             */
            'status'           => $this->status,
        ];
    }
}
