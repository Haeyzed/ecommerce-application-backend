<?php

namespace App\Services\Tenant;

use App\Models\Tenant\Order;
use App\Models\Tenant\Payment;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Class PaymentService
 * * Handles business logic related to tenant order payments and webhooks.
 */
class PaymentService
{
    /**
     * Create a new PaymentService instance.
     */
    public function __construct(
        private readonly OrderService $orderService
    ) {}

    /**
     * Record a new payment against an order.
     *
     * @param  array  $data  Validated payment data.
     *
     * @throws Throwable
     */
    public function recordPayment(Order $order, array $data): Payment
    {
        return DB::transaction(function () use ($order, $data) {
            $status = $data['status'] ?? 'pending';

            $payment = Payment::query()->create([
                'order_id' => $order->id,
                'provider' => $data['provider'],
                'provider_ref' => $data['provider_ref'] ?? null,
                'amount' => $data['amount'] ?? $order->total,
                'currency' => $data['currency'] ?? $order->currency,
                'status' => $status,
                'paid_at' => $status === 'succeeded' ? now() : null,
                'meta' => $data['meta'] ?? null,
            ]);

            if ($payment->status === 'succeeded') {
                $this->orderService->markOrderPaid($order);
            }

            return $payment;
        });
    }

    /**
     * Handle incoming provider webhooks to update payment status.
     */
    public function handleProviderWebhook(string $providerRef, string $status, array $meta = []): ?Payment
    {
        $payment = Payment::query()->where('provider_ref', $providerRef)->first();

        if (! $payment) {
            return null;
        }

        $payment->status = $status;
        $payment->meta = array_merge($payment->meta ?? [], $meta);

        if ($status === 'succeeded') {
            $payment->paid_at = now();
            $this->orderService->markOrderPaid($payment->order);
        }

        $payment->save();

        return $payment->fresh();
    }
}
