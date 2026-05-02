<?php

namespace App\Http\Controllers\Tenant\Api\HR;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\HR\CheckInRequest;
use App\Http\Resources\Tenant\HR\AttendanceResource;
use App\Models\Tenant\HR\Employee;
use App\Services\Tenant\HR\AttendanceService;
use DateMalformedStringException;
use DateTimeImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

// use App\Http\Requests\Tenant\HR\CheckOutRequest; // Recommended if you plan to separate validation rules

/**
 * Attendance Endpoints
 * Handles employee check-ins, check-outs, and timesheet records.
 */
class AttendanceController extends Controller
{
    public function __construct(
        private readonly AttendanceService $attendanceService
    ) {}

    /**
     * List all attendance records.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->integer('per_page', 30);

        $filters = [
            'employee_id' => $request->integer('employee_id'),
            'from' => $request->string('from'),
            'to' => $request->string('to'),
        ];

        $attendances = $this->attendanceService->getPaginatedAttendances($filters, $perPage);

        return ApiResponse::success(
            data: AttendanceResource::collection($attendances),
            message: 'Attendance records retrieved successfully',
            meta: ApiResponse::meta($attendances)
        );
    }

    /**
     * Record an employee check-in.
     *
     * @throws DateMalformedStringException
     */
    public function checkIn(CheckInRequest $request): JsonResponse
    {
        $employee = Employee::query()->findOrFail($request->integer('employee_id'));
        $timestamp = $request->string('at')->value() ? new DateTimeImmutable($request->string('at')) : new DateTimeImmutable;

        $attendance = $this->attendanceService->checkInEmployee($employee, $timestamp);

        return ApiResponse::success(
            new AttendanceResource($attendance),
            'Check-in recorded successfully',
            null,
            201
        );
    }

    /**
     * Record an employee check-out.
     *
     * @throws DateMalformedStringException
     */
    public function checkOut(CheckInRequest $request): JsonResponse
    {
        $employee = Employee::query()->findOrFail($request->integer('employee_id'));
        $timestamp = $request->string('at')->value() ? new DateTimeImmutable($request->string('at')) : new DateTimeImmutable;

        $attendance = $this->attendanceService->checkOutEmployee($employee, $timestamp);

        return ApiResponse::success(
            new AttendanceResource($attendance),
            'Check-out recorded successfully'
        );
    }
}
