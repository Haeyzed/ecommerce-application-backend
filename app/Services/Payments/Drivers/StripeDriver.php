<?php

namespace App\Services\Payments\Drivers;

use App\Contracts\Payments\PaymentGatewayInterface;
use App\Traits\ResolvesTenantPaymentConfig;
use Stripe\StripeClient;
use Stripe\Webhook;

/**
 * Class StripeDriver
 * * Implementation of the PaymentGatewayInterface for Stripe.
 */
class StripeDriver implements PaymentGatewayInterface
{
    use ResolvesTenantPaymentConfig;

    protected StripeClient $stripe;

    protected string $webhookSecret;

    /**
     * Create a new StripeDriver instance.
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
    public function initialize(array $payload): array
    {
        $intent = $this->stripe->paymentIntents->create([
            'amount' => (int) $payload['amount_minor'],
            'currency' => strtolower($payload['currency'] ?? 'usd'),
            'metadata' => $payload['metadata'] ?? [],
            'description' => $payload['description'] ?? null,
        ]);

        return [
            'provider' => $this->name(),
            'reference' => $intent->id,
            'redirect_url' => null,
            'client_secret' => $intent->client_secret,
            'raw' => $intent->toArray(),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function verify(string $reference, array $context = []): array
    {
        $intent = $this->stripe->paymentIntents->retrieve($reference);

        return [
            'status' => $intent->status,
            'raw' => $intent->toArray(),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function refund(string $reference, ?int $amountInMinor = null, ?string $reason = null): array
    {
        $response = $this->stripe->refunds->create(array_filter([
            'payment_intent' => $reference,
            'amount' => $amountInMinor,
            'reason' => $reason,
        ]));

        return $response->toArray();
    }

    /**
     * {@inheritDoc}
     */
    public function parseWebhook(string $rawBody, array $headers): array
    {
        $signature = $headers['stripe-signature'][0] ?? ($headers['Stripe-Signature'][0] ?? '');
        $secret = config('services.stripe.webhook_secret');

        $event = Webhook::constructEvent($rawBody, $signature, $secret);

        return $event->toArray();
    }

    /**
     * {@inheritDoc}
     */
    public function name(): string
    {
        return 'stripe';
    }
}
