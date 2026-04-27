<?php

namespace App\Services\Central;

use App\Contracts\Payments\SubscriptionGatewayInterface;
use App\Enums\Tenant\SubscriptionStatus;
use App\Models\Central\Invoice;
use App\Models\Central\Plan;
use App\Models\Central\Subscription;
use App\Models\Central\Tenant;
use App\Models\Central\User;
use App\Notifications\Central\DynamicTemplateNotification;
use App\Services\Payments\SubscriptionGatewayManager;
use Illuminate\Support\Facades\Notification;

/**
 * Class TenantSubscriptionService
 * * Handles business logic for tenant subscriptions interacting with external payment gateways (e.g., Stripe, PayPal).
 */
class TenantSubscriptionService
{
    /**
     * Create a new TenantSubscriptionService instance.
     */
    public function __construct(
        protected readonly SubscriptionGatewayManager $gateways
    ) {}

    /**
     * Resolve the appropriate payment gateway driver.
     */
    protected function gateway(?string $provider = null): SubscriptionGatewayInterface
    {
        return $this->gateways->driver($provider ?? config('payments.default'));
    }

    /**
     * Start a trial subscription for a tenant.
     */
    public function startTrial(Tenant $tenant, Plan $plan, int $days = 14): Subscription
    {
        $subscription = Subscription::query()->updateOrCreate(
            ['tenant_id' => $tenant->id],
            [
                'plan_id' => $plan->id,
                'status' => SubscriptionStatus::TRIAL->value,
                'trial_ends_at' => now()->addDays($days),
                'current_period_ends_at' => now()->addDays($days),
            ]
        );

        $this->notifyTenantSubscribed($tenant, $plan);

        return $subscription;
    }

    /**
     * Activate a subscription with a payment provider.
     */
    public function activate(Subscription $sub, string $provider, string $providerSubId, string $customerId): Subscription
    {
        $sub->update([
            'provider' => $provider,
            'provider_subscription_id' => $providerSubId,
            'provider_customer_id' => $customerId,
            'status' => SubscriptionStatus::ACTIVE->value,
            'current_period_ends_at' => now()->addMonth(),
        ]);

        $tenant = $sub->tenant;
        $plan = $sub->plan;

        if ($tenant && $plan) {
            $this->notifyTenantSubscribed($tenant, $plan);
        }

        return $sub->fresh();
    }

    /**
     * Cancel a subscription, optionally at the end of the billing period.
     */
    public function cancel(Subscription $sub, bool $atPeriodEnd = true): Subscription
    {
        if ($sub->provider && $sub->provider_subscription_id) {
            $this->gateway($sub->provider)->cancel($sub->provider_subscription_id, $atPeriodEnd);
        }

        $sub->update([
            'status' => SubscriptionStatus::CANCELED->value,
            'cancels_at' => $atPeriodEnd ? $sub->current_period_ends_at : now(),
        ]);

        $tenant = $sub->tenant;
        $plan = $sub->plan;

        if ($tenant && $plan) {
            $this->notifyTenantCanceled($tenant, $plan);
        }

        return $sub->fresh();
    }

    /**
     * Issue an invoice via the payment gateway and record it locally.
     */
    public function invoice(Subscription $sub, int $amountMinor, string $currency, string $description): Invoice
    {
        $raw = $this->gateway($sub->provider)->invoice($sub->provider_customer_id, $amountMinor, $currency, $description);

        return Invoice::query()->create([
            'tenant_id' => $sub->tenant_id,
            'subscription_id' => $sub->id,
            'amount_minor' => $amountMinor,
            'currency' => $currency,
            'description' => $description,
            'provider' => $sub->provider,
            'provider_invoice_id' => $raw['id'] ?? $raw['data']['id'] ?? $raw['data']['code'] ?? null,
        ]);
    }

    /**
     * Notify the tenant owner of a subscription event.
     */
    protected function notifyTenantSubscribed(Tenant $tenant, Plan $plan): void
    {
        if (!empty($tenant->owner_email)) {
            $user = User::query()->where('email', $tenant->owner_email)->first();
            if ($user) {
                $user->notify(new DynamicTemplateNotification(
                    event: 'tenant_subscribed',
                    templateData: [
                        'tenant_name' => $tenant->name,
                        'plan_name' => $plan->name,
                    ]
                ));
            } else {
                Notification::route('mail', $tenant->owner_email)
                    ->notify(new DynamicTemplateNotification(
                        event: 'tenant_subscribed',
                        templateData: [
                            'tenant_name' => $tenant->name,
                            'plan_name' => $plan->name,
                        ]
                    ));
            }
        }
    }

    /**
     * Notify the tenant owner of a cancellation event.
     */
    protected function notifyTenantCanceled(Tenant $tenant, Plan $plan): void
    {
        if (!empty($tenant->owner_email)) {
            $user = User::query()->where('email', $tenant->owner_email)->first();
            if ($user) {
                $user->notify(new DynamicTemplateNotification(
                    event: 'tenant_canceled',
                    templateData: [
                        'tenant_name' => $tenant->name,
                        'plan_name' => $plan->name,
                    ]
                ));
            } else {
                Notification::route('mail', $tenant->owner_email)
                    ->notify(new DynamicTemplateNotification(
                        event: 'tenant_canceled',
                        templateData: [
                            'tenant_name' => $tenant->name,
                            'plan_name' => $plan->name,
                        ]
                    ));
            }
        }
    }
}
