<?php

namespace App\Http\Resources\Tenant\HR;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobPostingResource extends JsonResource
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
             * The unique identifier for the job posting.
             * @var int $id
             * @example 3
             */
            'id'              => $this->id,

            /**
             * The title of the job posting.
             * @var string $title
             * @example "Senior Backend Engineer"
             */
            'title'           => $this->title,

            /**
             * The URL-friendly slug for the job posting.
             * @var string $slug
             * @example "senior-backend-engineer"
             */
            'slug'            => $this->slug,

            /**
             * The ID of the department advertising the position.
             * @var int|null $department_id
             * @example 2
             */
            'department_id'   => $this->department_id,

            /**
             * The detailed description of the job posting.
             * @var string $description
             * @example "We are looking for an experienced backend developer..."
             */
            'description'     => $this->description,

            /**
             * The type of employment (e.g., full_time, part_time).
             * @var string|null $employment_type
             * @example "full_time"
             */
            'employment_type' => $this->employment_type,

            /**
             * The physical or remote location of the job.
             * @var string|null $location
             * @example "Remote"
             */
            'location'        => $this->location,

            /**
             * Indicates whether the job posting is currently accepting applications.
             * @var bool $is_open
             * @example true
             */
            'is_open'         => (bool) $this->is_open,

            /**
             * The date and time when the job posting automatically closes.
             * @var string|null $closes_at
             * @example "2026-06-30 23:59:59"
             */
            'closes_at'       => $this->closes_at,
        ];
    }
}
