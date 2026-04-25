<?php

namespace App\Contracts\Payments;

/**
 * Interface PaymentGatewayInterface
 *
 * Common surface every payment provider (e.g., Stripe, Paystack) must expose.
 * Drivers receive whatever the controller has — keeping the array loose so tenants can pass extra metadata.
 */
interface PaymentGatewayInterface
{
    /**
     * Initialize a one-shot payment intent or transaction.
     *
     * @param  array  $payload  The payment payload data.
     * @return array{provider:string, reference:string, redirect_url:?string, raw:array}
     */
    public function initialize(array $payload): array;

    /**
     * Verify a webhook or callback by reference or signature.
     *
     * @param  string  $reference  The transaction reference.
     * @param  array  $context  Additional context data.
     */
    public function verify(string $reference, array $context = []): array;

    /**
     * Refund a captured payment.
     *
     * @param  string  $reference  The original transaction reference.
     * @param  int|null  $amountInMinor  The amount to refund in minor units (e.g., cents).
     * @param  string|null  $reason  The reason for the refund.
     */
    public function refund(string $reference, ?int $amountInMinor = null, ?string $reason = null): array;

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
