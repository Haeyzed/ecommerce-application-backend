<?php

namespace App\Http\Controllers\Central\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Central\Onboarding\OnboardTenantRequest;
use App\Services\Central\TenantOnboardingService;
use Illuminate\Http\JsonResponse;
use Throwable;

/**
 * Onboarding Endpoints
 * Handles the public sign-up and initialization of new tenant stores.
 */
class OnboardingController extends Controller
{
    /**
     * Create a new OnboardingController instance.
     */
    public function __construct(
        private readonly TenantOnboardingService $onboardingService
    ) {}

    /**
     * Onboard a new tenant store.
     *
     * @throws Throwable
     */
    public function store(OnboardTenantRequest $request): JsonResponse
    {
        $tenant = $this->onboardingService->onboard($request->validated());

        return ApiResponse::success(
            $tenant->load('domains'),
            'Store successfully onboarded and ready',
            null,
            201
        );
    }
}
