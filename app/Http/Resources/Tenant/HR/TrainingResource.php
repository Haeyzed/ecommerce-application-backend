<?php

namespace App\Http\Resources\Tenant\HR;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TrainingResource extends JsonResource
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
             * The unique identifier for the training session.
             *
             * @var int $id
             *
             * @example 7
             */
            'id' => $this->id,

            /**
             * The title of the training session.
             *
             * @var string $title
             *
             * @example "Cybersecurity Awareness 2026"
             */
            'title' => $this->title,

            /**
             * A detailed description of the training.
             *
             * @var string|null $description
             *
             * @example "Annual mandatory security compliance training."
             */
            'description' => $this->description,

            /**
             * The start date and time of the training.
             *
             * @var string|null $starts_at
             *
             * @example "2026-06-01 09:00:00"
             */
            'starts_at' => $this->starts_at,

            /**
             * The end date and time of the training.
             *
             * @var string|null $ends_at
             *
             * @example "2026-06-01 12:00:00"
             */
            'ends_at' => $this->ends_at,

            /**
             * The physical or virtual location of the training.
             *
             * @var string|null $location
             *
             * @example "Conference Room A"
             */
            'location' => $this->location,

            /**
             * Indicates if the training is mandatory for employees.
             *
             * @var bool $is_mandatory
             *
             * @example true
             */
            'is_mandatory' => (bool) $this->is_mandatory,

            /**
             * The total number of employees enrolled in this training (if loaded).
             *
             * @var int|null $employees_count
             *
             * @example 45
             */
            'employees_count' => $this->whenCounted('employees'),
        ];
    }
}
