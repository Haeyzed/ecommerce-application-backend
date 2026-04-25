<?php

namespace App\Http\Resources\Tenant\HR;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
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
             * The unique identifier for the employee.
             * @var int $id
             * @example 42
             */
            'id'              => $this->id,

            /**
             * The unique internal code assigned to the employee.
             * @var string|null $employee_code
             * @example "EMP-0042"
             */
            'employee_code'   => $this->employee_code,

            /**
             * The first name of the employee.
             * @var string $first_name
             * @example "John"
             */
            'first_name'      => $this->first_name,

            /**
             * The last name of the employee.
             * @var string $last_name
             * @example "Doe"
             */
            'last_name'       => $this->last_name,

            /**
             * The email address of the employee.
             * @var string $email
             * @example "john.doe@company.com"
             */
            'email'           => $this->email,

            /**
             * The phone number of the employee.
             * @var string|null $phone
             * @example "+1234567890"
             */
            'phone'           => $this->phone,

            /**
             * The ID of the department the employee belongs to.
             * @var int|null $department_id
             * @example 3
             */
            'department_id'   => $this->department_id,

            /**
             * The ID of the position the employee holds.
             * @var int|null $position_id
             * @example 10
             */
            'position_id'     => $this->position_id,

            /**
             * The type of employment (e.g., full_time, part_time).
             * @var string|null $employment_type
             * @example "full_time"
             */
            'employment_type' => $this->employment_type,

            /**
             * The base salary of the employee in minor units (cents).
             * @var int|null $salary_cents
             * @example 7500000
             */
            'salary_cents'    => $this->salary_cents,

            /**
             * The date the employee was hired.
             * @var string|null $hired_at
             * @example "2026-01-15"
             */
            'hired_at'        => $this->hired_at,

            /**
             * The date the employee was terminated, if applicable.
             * @var string|null $terminated_at
             * @example null
             */
            'terminated_at'   => $this->terminated_at,

            /**
             * Indicates if the employee is currently active.
             * @var bool $is_active
             * @example true
             */
            'is_active'       => (bool) $this->is_active,

            /**
             * The loaded department relation for the employee.
             * @var DepartmentResource|null $department
             */
            'department'      => new DepartmentResource($this->whenLoaded('department')),

            /**
             * The loaded position relation for the employee.
             * @var PositionResource|null $position
             */
            'position'        => new PositionResource($this->whenLoaded('position')),

            /**
             * The URL to the employee's avatar image.
             * @var string|null $avatar_url
             * @example "https://example.com/media/avatar.jpg"
             */
            'avatar_url'      => method_exists($this->resource, 'getFirstMediaUrl') ? $this->getFirstMediaUrl('default') : null,
        ];
    }
}
