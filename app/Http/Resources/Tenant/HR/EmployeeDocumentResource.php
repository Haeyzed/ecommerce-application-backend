<?php

namespace App\Http\Resources\Tenant\HR;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeDocumentResource extends JsonResource
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
             * The unique identifier for the employee document.
             *
             * @var int $id
             *
             * @example 15
             */
            'id' => $this->id,

            /**
             * The ID of the employee this document belongs to.
             *
             * @var int $employee_id
             *
             * @example 42
             */
            'employee_id' => $this->employee_id,

            /**
             * The title of the document.
             *
             * @var string $title
             *
             * @example "Signed Employment Contract"
             */
            'title' => $this->title,

            /**
             * The type of the document (contract, id, certificate, etc.).
             *
             * @var string|null $type
             *
             * @example "contract"
             */
            'type' => $this->type,

            /**
             * The expiration date of the document, if applicable.
             *
             * @var string|null $expires_at
             *
             * @example "2027-12-31"
             */
            'expires_at' => $this->expires_at,

            /**
             * Additional notes regarding the document.
             *
             * @var string|null $notes
             *
             * @example "Original stored in HR filing cabinet."
             */
            'notes' => $this->notes,
        ];
    }
}
