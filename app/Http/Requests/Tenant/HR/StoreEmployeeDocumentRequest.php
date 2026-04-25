<?php

namespace App\Http\Requests\Tenant\HR;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;

class StoreEmployeeDocumentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            /**
             * The ID of the employee this document belongs to.
             * @var int $employee_id
             * @example 22
             */
            'employee_id' => ['required', 'integer', 'exists:employees,id'],

            /**
             * The title of the document.
             * @var string $title
             * @example "Employment Contract"
             */
            'title' => ['required', 'string', 'max:255'],

            /**
             * The type of the document (contract, id, certificate, etc.).
             * @var string|null $type
             * @example "contract"
             */
            'type' => ['nullable', 'string', 'max:64'],

            /**
             * The expiration date of the document.
             * @var string|null $expires_at
             * @example "2027-12-31"
             */
            'expires_at' => ['nullable', 'date'],

            /**
             * Additional notes regarding the document.
             * @var string|null $notes
             * @example "Signed original copy kept in HR filing cabinet."
             */
            'notes' => ['nullable', 'string'],

            /**
             * The actual file being uploaded.
             * @var \Illuminate\Http\UploadedFile $file
             * @example "contract_signed.pdf"
             */
            'file' => ['required', 'file', 'max:10240'],
        ];
    }
}
