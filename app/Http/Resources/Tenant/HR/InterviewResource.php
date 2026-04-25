<?php

namespace App\Http\Resources\Tenant\HR;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InterviewResource extends JsonResource
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
             * The unique identifier for the interview.
             * @var int $id
             * @example 8
             */
            'id'                      => $this->id,

            /**
             * The ID of the applicant being interviewed.
             * @var int $applicant_id
             * @example 14
             */
            'applicant_id'            => $this->applicant_id,

            /**
             * The ID of the employee conducting the interview.
             * @var int|null $interviewer_employee_id
             * @example 42
             */
            'interviewer_employee_id' => $this->interviewer_employee_id,

            /**
             * The scheduled date and time for the interview.
             * @var string $scheduled_at
             * @example "2026-05-10 14:00:00"
             */
            'scheduled_at'            => $this->scheduled_at,

            /**
             * The mode of the interview (e.g., onsite, video, phone).
             * @var string|null $mode
             * @example "video"
             */
            'mode'                    => $this->mode,

            /**
             * The status of the interview.
             * @var string $status
             * @example "scheduled"
             */
            'status'                  => $this->status,

            /**
             * The score assigned after the interview.
             * @var int|float|null $score
             * @example 8.5
             */
            'score'                   => $this->score,

            /**
             * Notes taken during or after the interview.
             * @var string|null $notes
             * @example "Strong technical skills, good cultural fit."
             */
            'notes'                   => $this->notes,
        ];
    }
}
