<?php

namespace App\Services\Central;

use App\Enums\Central\SubscriptionStatus;
use App\Models\Central\Invoice;
use App\Models\Central\Plan;
use App\Models\Central\Subscription;
use App\Models\Central\Tenant;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

/**
 * Class SubscriptionService
 * * Handles business logic related to central tenant subscriptions.
 */
class SubscriptionService
{
    /**
     * Retrieve a paginated list of subscriptions.
     */
    public function getPaginatedSubscriptions(int $perPage = 20): LengthAwarePaginator
    {
        return Subscription::query()
            ->with(['tenant', 'plan'])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Start a trial subscription for a tenant.
     */
    public function startTrial(Tenant $tenant, Plan $plan, int $trialDays = 14): Subscription
    {
        return Subscription::query()->create([
            'tenant_id' => $tenant->id,
            'plan_id' => $plan->id,
            'status' => SubscriptionStatus::TRIAL->value,
            'trial_ends_at' => now()->addDays($trialDays),
            'current_period_start' => now(),
            'current_period_end' => now()->addDays($trialDays),
        ]);
    }

    /**
     * Activate a subscription.
     */
    public function activate(Subscription $subscription): Subscription
    {
        $subscription->update([
            'status' => 'active',
            'current_period_start' => now(),
            'current_period_end' => now()->addMonth(),
        ]);

        return $subscription->fresh();
    }

    /**
     * Cancel a subscription.
     */
    public function cancel(Subscription $subscription): Subscription
    {
        $subscription->update([
            'status' => SubscriptionStatus::CANCELLED->value,
            'cancelled_at' => now(),
        ]);

        return $subscription->fresh();
    }

    /**
     * Issue an invoice for a subscription.
     */
    public function issueInvoice(Subscription $subscription): Invoice
    {
        return Invoice::query()->create([
            'tenant_id' => $subscription->tenant_id,
            'subscription_id' => $subscription->id,
            'number' => 'INV-'.strtoupper(Str::random(8)),
            'amount' => $subscription->plan->price ?? 0,
            'currency' => $subscription->plan->currency ?? 'USD',
            'status' => 'open',
            'issued_at' => now(),
        ]);
    }
}
