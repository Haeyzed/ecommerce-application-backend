<?php

namespace App\Http\Controllers\Tenant\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Payment\StorePaymentRequest;
use App\Models\Tenant\Order;
use App\Services\Tenant\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

/**
 * Payment Endpoints
 * * Handles payment recording and external provider webhooks.
 */
class PaymentController extends Controller
{
    /**
     * Create a new PaymentController instance.
     */
    public function __construct(
        private readonly PaymentService $paymentService
    ) {
        $this->middleware('permission:process payments')->only(['store']);
        // The webhook method is an external endpoint and should not be protected by internal permissions.
    }

    /**
     * Record a new payment.
     *
     * @throws Throwable
     */
    public function store(StorePaymentRequest $request, Order $order): JsonResponse
    {
        $payment = $this->paymentService->recordPayment($order, $request->validated());

        return ApiResponse::success(
            ['payment' => $payment],
            'Payment recorded successfully',
            null,
            201
        );
    }

    /**
     * Provider webhook endpoint (e.g. Stripe). Tenant-scoped via host.
     */
    public function webhook(Request $request): JsonResponse
    {
        $payment = $this->paymentService->handleProviderWebhook(
            (string) $request->input('provider_ref'),
            (string) $request->input('status'),
            (array) $request->input('meta', [])
        );

        return ApiResponse::success(
            ['payment' => $payment, 'ok' => true],
            'Webhook processed'
        );
    }
}
