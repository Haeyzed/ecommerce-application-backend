<?php

namespace App\Services\Payments\Drivers;

use App\Contracts\Payments\SubscriptionGatewayInterface;
use App\Traits\ResolvesTenantPaymentConfig;
use Stripe\StripeClient;
use Stripe\Webhook;

/**
 * Class StripeSubscriptionDriver
 * * Implementation of the SubscriptionGatewayInterface for Stripe.
 */
class StripeSubscriptionDriver implements SubscriptionGatewayInterface
{
    use ResolvesTenantPaymentConfig;

    protected StripeClient $stripe;

    protected string $webhookSecret;

    /**
     * Create a new StripeSubscriptionDriver instance.
     */
    public function __construct()
    {
        $credentials = $this->putEnv('stripe');
        $this->stripe = new StripeClient($credentials['secret_key'] ?? '');
        $this->webhookSecret = $credentials['webhook_secret'] ?? '';
    }

    /**
     * {@inheritDoc}
     */
    public function ensureCustomer(array $owner): string
    {
        if (! empty($owner['provider_customer_id'])) {
            return $owner['provider_customer_id'];
        }

        $customer = $this->stripe->customers->create([
            'email' => $owner['email'] ?? null,
            'name' => $owner['name'] ?? null,
            'metadata' => ['tenant_id' => $owner['tenant_id'] ?? null],
        ]);

        return $customer->id;
    }

    /**
     * {@inheritDoc}
     */
    public function subscribe(string $customerId, string $planCode, array $opts = []): array
    {
        $subscription = $this->stripe->subscriptions->create([
            'customer' => $customerId,
            'items' => [['price' => $planCode]],
            'trial_period_days' => $opts['trial_days'] ?? null,
            'metadata' => $opts['metadata'] ?? [],
        ]);

        return $subscription->toArray();
    }

    /**
     * {@inheritDoc}
     */
    public function cancel(string $subscriptionId, bool $atPeriodEnd = true): array
    {
        return $atPeriodEnd
            ? $this->stripe->subscriptions->update($subscriptionId, ['cancel_at_period_end' => true])->toArray()
            : $this->stripe->subscriptions->cancel($subscriptionId)->toArray();
    }

    /**
     * {@inheritDoc}
     */
    public function resume(string $subscriptionId): array
    {
        return $this->stripe->subscriptions->update($subscriptionId, ['cancel_at_period_end' => false])->toArray();
    }

    /**
     * {@inheritDoc}
     */
    public function invoice(string $customerId, int $amountInMinor, string $currency, string $description): array
    {
        $this->stripe->invoiceItems->create([
            'customer' => $customerId,
            'amount' => $amountInMinor,
            'currency' => strtolower($currency),
            'description' => $description,
        ]);

        $invoice = $this->stripe->invoices->create([
            'customer' => $customerId,
            'auto_advance' => true,
        ]);

        return $invoice->toArray();
    }

    /**
     * {@inheritDoc}
     */
    public function parseWebhook(string $rawBody, array $headers): array
    {
        $signature = $headers['stripe-signature'][0] ?? ($headers['Stripe-Signature'][0] ?? '');
        $secret = config('services.stripe.webhook_secret');

        return Webhook::constructEvent($rawBody, $signature, $secret)->toArray();
    }

    /**
     * {@inheritDoc}
     */
    public function name(): string
    {
        return 'stripe';
    }
}
