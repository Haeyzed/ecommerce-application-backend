<?php

namespace App\Http\Controllers\Central\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Central\Subscription\StartTrialRequest;
use App\Models\Central\Plan;
use App\Models\Central\Subscription;
use App\Models\Central\Tenant;
use App\Services\Central\SubscriptionService;
use Illuminate\Http\JsonResponse;

/**
 * Subscription Endpoints
 * Handles tenant subscription lifecycles on the central platform.
 */
class SubscriptionController extends Controller
{
    /**
     * Create a new SubscriptionController instance.
     */
    public function __construct(
        private readonly SubscriptionService $subscriptionService
    ) {}

    /**
     * List all subscriptions.
     */
    public function index(): JsonResponse
    {
        $subscriptions = $this->subscriptionService->getPaginatedSubscriptions();

        return ApiResponse::success(
            ['subscriptions' => $subscriptions],
            'Subscriptions retrieved successfully'
        );
    }

    /**
     * Start a new subscription trial.
     */
    public function startTrial(StartTrialRequest $request): JsonResponse
    {
        $tenant = Tenant::query()->findOrFail($request->validated('tenant_id'));
        $plan = Plan::query()->findOrFail($request->validated('plan_id'));

        $subscription = $this->subscriptionService->startTrial(
            $tenant,
            $plan,
            (int) $request->validated('trial_days', 14)
        );

        return ApiResponse::success(
            ['subscription' => $subscription],
            'Trial started successfully',
            null,
            201
        );
    }

    /**
     * Activate a subscription.
     */
    public function activate(Subscription $subscription): JsonResponse
    {
        $activatedSub = $this->subscriptionService->activate($subscription);

        return ApiResponse::success(
            ['subscription' => $activatedSub],
            'Subscription activated successfully'
        );
    }

    /**
     * Cancel a subscription.
     */
    public function cancel(Subscription $subscription): JsonResponse
    {
        $cancelledSub = $this->subscriptionService->cancel($subscription);

        return ApiResponse::success(
            ['subscription' => $cancelledSub],
            'Subscription cancelled successfully'
        );
    }

    /**
     * Issue an invoice for the subscription.
     */
    public function invoice(Subscription $subscription): JsonResponse
    {
        $invoice = $this->subscriptionService->issueInvoice($subscription);

        return ApiResponse::success(
            ['invoice' => $invoice],
            'Invoice issued successfully',
            null,
            201
        );
    }
}
