<?php

namespace App\Http\Controllers\Tenant\Api\HR;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\HR\StoreTrainingRequest;
use App\Http\Requests\Tenant\HR\UpdateTrainingRequest;
use App\Http\Resources\Tenant\HR\TrainingResource;
use App\Models\Tenant\HR\Employee;
use App\Models\Tenant\HR\Training;
use App\Services\Tenant\HR\TrainingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Training Endpoints
 * * Handles creation of training modules and employee enrollment tracking.
 */
class TrainingController extends Controller
{
    public function __construct(
        private readonly TrainingService $trainingService
    ) {}

    /**
     * List all training sessions.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->integer('per_page', 20);
        $trainings = $this->trainingService->getPaginatedTrainings($request->all(), $perPage);

        return ApiResponse::success(
            data: TrainingResource::collection($trainings),
            message: 'Trainings retrieved successfully',
            meta: ApiResponse::meta($trainings)
        );
    }

    /**
     * Show a specific training session.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $training = Training::query()->findOrFail($id);

        return ApiResponse::success(
            new TrainingResource($training),
            'Training retrieved successfully'
        );
    }

    /**
     * Create a new training session.
     *
     * @param StoreTrainingRequest $request
     * @return JsonResponse
     */
    public function store(StoreTrainingRequest $request): JsonResponse
    {
        $training = $this->trainingService->createTraining($request->validated());

        return ApiResponse::success(
            new TrainingResource($training),
            'Training created successfully',
            null,
            201
        );
    }

    /**
     * Update an existing training session.
     *
     * @param UpdateTrainingRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateTrainingRequest $request, int $id): JsonResponse
    {
        $training = Training::query()->findOrFail($id);
        $updatedTraining = $this->trainingService->updateTraining($training, $request->validated());

        return ApiResponse::success(
            new TrainingResource($updatedTraining),
            'Training updated successfully'
        );
    }

    /**
     * Enroll an employee in a specific training session.
     *
     * @param Request $request
     * @param int $id The ID of the training session.
     * @return JsonResponse
     */
    public function enroll(Request $request, int $id): JsonResponse
    {
        $request->validate(['employee_id' => 'required|integer|exists:employees,id']);

        $training = Training::query()->findOrFail($id);
        $employee = Employee::query()->findOrFail($request->integer('employee_id'));

        $this->trainingService->enrollEmployee($training, $employee);

        return ApiResponse::success(['enrolled' => true], 'Employee enrolled successfully');
    }

    /**
     * Mark an employee's training as completed.
     *
     * @param Request $request
     * @param int $id The ID of the training session.
     * @return JsonResponse
     */
    public function complete(Request $request, int $id): JsonResponse
    {
        $request->validate(['employee_id' => 'required|integer|exists:employees,id']);

        $training = Training::query()->findOrFail($id);
        $employee = Employee::query()->findOrFail($request->integer('employee_id'));

        $this->trainingService->completeTraining($training, $employee);

        return ApiResponse::success(['completed' => true], 'Training marked as completed');
    }

    /**
     * Delete a training session.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $training = Training::query()->findOrFail($id);
        $this->trainingService->deleteTraining($training);

        return ApiResponse::success(null, 'Training deleted successfully');
    }
}
