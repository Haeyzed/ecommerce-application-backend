<?php

namespace App\Http\Resources\Tenant\HR;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PositionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            /**
             * The unique identifier for the position.
             *
             * @var int $id
             *
             * @example 10
             */
            'id' => $this->id,

            /**
             * The ID of the department this position belongs to.
             *
             * @var int $department_id
             *
             * @example 2
             */
            'department_id' => $this->department_id,

            /**
             * The job title for the position.
             *
             * @var string $title
             *
             * @example "Senior Frontend Developer"
             */
            'title' => $this->title,

            /**
             * The minimum expected salary boundary in minor units (cents).
             *
             * @var int|null $min_salary_cents
             *
             * @example 7000000
             */
            'min_salary_cents' => $this->min_salary_cents,

            /**
             * The maximum expected salary boundary in minor units (cents).
             *
             * @var int|null $max_salary_cents
             *
             * @example 12000000
             */
            'max_salary_cents' => $this->max_salary_cents,
        ];
    }
}
