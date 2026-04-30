<?php

namespace App\Http\Resources\Tenant\HR;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeaveRequestResource extends JsonResource
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
             * The unique identifier for the leave request.
             *
             * @var int $id
             *
             * @example 22
             */
            'id' => $this->id,

            /**
             * The ID of the employee requesting leave.
             *
             * @var int $employee_id
             *
             * @example 42
             */
            'employee_id' => $this->employee_id,

            /**
             * The type of leave (e.g., sick, vacation, maternity).
             *
             * @var string $type
             *
             * @example "vacation"
             */
            'type' => $this->type,

            /**
             * The start date of the leave.
             *
             * @var string $start_date
             *
             * @example "2026-05-15"
             */
            'start_date' => $this->start_date,

            /**
             * The end date of the leave.
             *
             * @var string $end_date
             *
             * @example "2026-05-20"
             */
            'end_date' => $this->end_date,

            /**
             * The calculated total days requested.
             *
             * @var float $days
             *
             * @example 5.0
             */
            'days' => (float) $this->days,

            /**
             * The reason provided for the leave.
             *
             * @var string|null $reason
             *
             * @example "Annual family trip."
             */
            'reason' => $this->reason,

            /**
             * The current status of the leave request (e.g., pending, approved, rejected).
             *
             * @var string $status
             *
             * @example "approved"
             */
            'status' => $this->status,

            /**
             * The ID of the employee who approved or rejected the request.
             *
             * @var int|null $approved_by_employee_id
             *
             * @example 10
             */
            'approved_by_employee_id' => $this->approved_by_employee_id,
        ];
    }
}
