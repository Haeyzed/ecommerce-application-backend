<?php

namespace App\Http\Resources\Tenant\HR;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DepartmentResource extends JsonResource
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
             * The unique identifier for the department.
             * @var int $id
             * @example 3
             */
            'id' => $this->id,

            /**
             * The name of the department.
             * @var string $name
             * @example "Engineering"
             */
            'name' => $this->name,

            /**
             * An internal code or abbreviation for the department.
             * @var string|null $code
             * @example "ENG-01"
             */
            'code' => $this->code,

            /**
             * The ID of the parent department, if this is a sub-department.
             * @var int|null $parent_id
             * @example 1
             */
            'parent_id' => $this->parent_id,

            /**
             * The ID of the employee who manages this department.
             * @var int|null $manager_employee_id
             * @example 45
             */
            'manager_employee_id' => $this->manager_employee_id,

            /**
             * The total number of employees in this department (if loaded).
             * @var int|null $employees_count
             * @example 12
             */
            'employees_count' => $this->whenCounted('employees'),
        ];
    }
}
