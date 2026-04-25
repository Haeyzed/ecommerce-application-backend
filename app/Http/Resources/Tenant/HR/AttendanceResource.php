<?php

namespace App\Http\Resources\Tenant\HR;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceResource extends JsonResource
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
             * The unique identifier for the attendance record.
             * @var int $id
             * @example 1024
             */
            'id' => $this->id,

            /**
             * The ID of the employee this record belongs to.
             * @var int $employee_id
             * @example 42
             */
            'employee_id' => $this->employee_id,

            /**
             * The specific date of the attendance record.
             * @var string $date
             * @example "2026-04-25"
             */
            'date' => $this->date,

            /**
             * The precise date and time the employee checked in.
             * @var string|null $check_in
             * @example "2026-04-25 08:55:00"
             */
            'check_in' => $this->check_in,

            /**
             * The precise date and time the employee checked out.
             * @var string|null $check_out
             * @example "2026-04-25 17:05:00"
             */
            'check_out' => $this->check_out,

            /**
             * The total number of minutes worked during this shift.
             * @var int|null $minutes_worked
             * @example 490
             */
            'minutes_worked' => $this->minutes_worked,

            /**
             * The status of the attendance (e.g., present, absent, leave).
             * @var string $status
             * @example "present"
             */
            'status' => $this->status,
        ];
    }
}
