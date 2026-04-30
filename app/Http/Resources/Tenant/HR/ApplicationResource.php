<?php

namespace App\Http\Resources\Tenant\HR;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicantResource extends JsonResource
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
             * The unique identifier for the applicant.
             *
             * @var int $id
             *
             * @example 25
             */
            'id' => $this->id,

            /**
             * The ID of the job posting the applicant applied to.
             *
             * @var int $job_posting_id
             *
             * @example 3
             */
            'job_posting_id' => $this->job_posting_id,

            /**
             * The applicant's first name.
             *
             * @var string $first_name
             *
             * @example "Alice"
             */
            'first_name' => $this->first_name,

            /**
             * The applicant's last name.
             *
             * @var string $last_name
             *
             * @example "Smith"
             */
            'last_name' => $this->last_name,

            /**
             * The applicant's email address.
             *
             * @var string $email
             *
             * @example "alice.smith@example.com"
             */
            'email' => $this->email,

            /**
             * The applicant's phone number.
             *
             * @var string|null $phone
             *
             * @example "+12345678900"
             */
            'phone' => $this->phone,

            /**
             * The current status of the application (e.g., pending, reviewing, interviewed, rejected, hired).
             *
             * @var string $status
             *
             * @example "pending"
             */
            'status' => $this->status,

            /**
             * The applicant's submitted cover letter text.
             *
             * @var string|null $cover_letter
             *
             * @example "I am writing to express my interest in the backend engineering position..."
             */
            'cover_letter' => $this->cover_letter,

            /**
             * The fully qualified URL or file path to the applicant's uploaded resume.
             *
             * @var string|null $resume_url
             *
             * @example "https://company.com/storage/resumes/alice_smith.pdf"
             */
            'resume_url' => $this->resume_path,
        ];
    }
}
