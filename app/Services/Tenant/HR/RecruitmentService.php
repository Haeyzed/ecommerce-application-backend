<?php

namespace App\Services\Tenant\HR;

use App\Models\Tenant\HR\Applicant;
use App\Models\Tenant\HR\Employee;
use App\Models\Tenant\HR\Interview;
use App\Models\Tenant\HR\JobPosting;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Class RecruitmentService
 * * Handles business logic related to job postings, applicants, and interviews.
 */
class RecruitmentService
{
    /**
     * Retrieve a paginated list of job postings.
     *
     * @param  array  $filters  Query filters (e.g., is_open).
     */
    public function getPaginatedPostings(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return JobPosting::query()
            ->with('department:id,name')
            ->when($filters['is_open'] ?? null, fn ($q) => $q->where('is_open', true))
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    /**
     * Create a job posting.
     */
    public function createPosting(array $data): JobPosting
    {
        return JobPosting::query()->create($data);
    }

    /**
     * Update a job posting.
     */
    public function updatePosting(JobPosting $posting, array $data): JobPosting
    {
        $posting->update($data);

        return $posting->fresh();
    }

    /**
     * Delete a job posting.
     */
    public function deletePosting(JobPosting $posting): void
    {
        $posting->delete();
    }

    /**
     * Retrieve a paginated list of applicants.
     *
     * @param  array  $filters  Query filters (e.g., job_posting_id, status).
     */
    public function getPaginatedApplicants(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return Applicant::query()
            ->with('jobPosting:id,title')
            ->filter($filters)
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    /**
     * Create a new applicant.
     */
    public function createApplicant(array $data): Applicant
    {
        return Applicant::query()->create($data);
    }

    /**
     * Move an applicant to a different status.
     */
    public function updateApplicantStatus(Applicant $applicant, string $status): Applicant
    {
        $applicant->update(['status' => $status]);

        return $applicant->fresh();
    }

    /**
     * Schedule an interview for an applicant.
     *
     * @param  array  $data  Validated interview data.
     */
    public function scheduleInterview(Applicant $applicant, ?Employee $interviewer, array $data): Interview
    {
        $applicant->update(['status' => 'interview']);

        return Interview::query()->create([
            'applicant_id' => $applicant->id,
            'interviewer_employee_id' => $interviewer?->id,
            'scheduled_at' => $data['scheduled_at'],
            'mode' => $data['mode'] ?? 'onsite',
            'status' => 'scheduled',
            'notes' => $data['notes'] ?? null,
        ]);
    }
}
