<?php

namespace App\Http\Requests\Tenant\HR;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;

class StoreApplicantRequest extends FormRequest
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
             * The ID of the job posting being applied to.
             * @var int $job_posting_id
             * @example 3
             */
            'job_posting_id' => ['required', 'integer', 'exists:job_postings,id'],

            /**
             * The applicant's first name.
             * @var string $first_name
             * @example "Jane"
             */
            'first_name' => ['required', 'string', 'max:128'],

            /**
             * The applicant's last name.
             * @var string $last_name
             * @example "Doe"
             */
            'last_name' => ['required', 'string', 'max:128'],

            /**
             * The applicant's email address.
             * @var string $email
             * @example "jane.doe@example.com"
             */
            'email' => ['required', 'email', 'max:255'],

            /**
             * The applicant's phone number.
             * @var string|null $phone
             * @example "+1234567890"
             */
            'phone' => ['nullable', 'string', 'max:32'],

            /**
             * The applicant's cover letter text.
             * @var string|null $cover_letter
             * @example "I am writing to express my interest in the position..."
             */
            'cover_letter' => ['nullable', 'string'],

            /**
             * The applicant's resume file upload (PDF, DOC, DOCX).
             * @var UploadedFile|null $resume
             * @example "resume.pdf"
             */
            'resume' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
        ];
    }
}
