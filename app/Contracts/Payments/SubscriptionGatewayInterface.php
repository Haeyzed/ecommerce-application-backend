<?php

namespace App\Contracts\Payments;

/**
 * Interface SubscriptionGatewayInterface
 *
 * Recurring billing surface used by the central SaaS layer to bill tenants for their subscription plans.
 * Mirrors what Cashier exposes but remains provider-agnostic to easily swap Stripe, Paystack, or future providers.
 */
interface SubscriptionGatewayInterface
{
    /**
     * Create or fetch the customer record at the provider.
     *
     * @param  array  $owner  Data representing the tenant or owner.
     * @return string The provider's customer ID.
     */
    public function ensureCustomer(array $owner): string;

    /**
     * Subscribe a customer to a specific plan or price.
     *
     * @param  string  $customerId  The provider's customer ID.
     * @param  string  $planCode  The provider-specific plan identifier.
     * @param  array  $opts  Additional subscription options.
     * @return array The subscription details.
     */
    public function subscribe(string $customerId, string $planCode, array $opts = []): array;

    /**
     * Cancel an active subscription.
     *
     * @param  string  $subscriptionId  The provider's subscription ID.
     * @param  bool  $atPeriodEnd  Whether to cancel at the end of the billing period.
     */
    public function cancel(string $subscriptionId, bool $atPeriodEnd = true): array;

    /**
     * Resume a previously canceled (but still within grace period) subscription.
     *
     * @param  string  $subscriptionId  The provider's subscription ID.
     */
    public function resume(string $subscriptionId): array;

    /**
     * Generate a one-off invoice for usage or overages.
     *
     * @param  string  $customerId  The provider's customer ID.
     * @param  int  $amountInMinor  The invoice amount in minor units (e.g., cents).
     * @param  string  $currency  The ISO currency code.
     * @param  string  $description  The description of the charge.
     */
    public function invoice(string $customerId, int $amountInMinor, string $currency, string $description): array;

    /**
     * Validate webhook signature and parse the payload.
     *
     * @param  string  $rawBody  The raw webhook request body.
     * @param  array  $headers  The webhook request headers.
     * @return array The parsed event payload.
     */
    public function parseWebhook(string $rawBody, array $headers): array;

    /**
     * Get the internal name of the gateway provider.
     */
    public function name(): string;
}
