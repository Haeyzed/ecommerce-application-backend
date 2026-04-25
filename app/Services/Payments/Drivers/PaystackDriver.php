<?php

namespace App\Services\Payments\Drivers;

use App\Contracts\Payments\PaymentGatewayInterface;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Class PaystackDriver
 * * Implementation of the PaymentGatewayInterface for Paystack.
 */
class PaystackDriver implements PaymentGatewayInterface
{
    protected string $secret;

    protected string $baseUrl;

    /**
     * Create a new PaystackDriver instance.
     */
    public function __construct()
    {
        $this->secret = (string) config('services.paystack.secret');
        $this->baseUrl = rtrim((string) config('services.paystack.url', 'https://api.paystack.co'), '/');
    }

    /**
     * Build the pre-configured HTTP client.
     */
    protected function client(): PendingRequest
    {
        return Http::withToken($this->secret)->acceptJson()->baseUrl($this->baseUrl);
    }

    /**
     * {@inheritDoc}
     *
     * @throws RequestException|ConnectionException
     */
    public function initialize(array $payload): array
    {
        $response = $this->client()->post('/transaction/initialize', [
            'email' => $payload['email'],
            'amount' => (int) $payload['amount_minor'], // in kobo
            'currency' => strtoupper($payload['currency'] ?? 'NGN'),
            'metadata' => $payload['metadata'] ?? [],
            'callback_url' => $payload['callback_url'] ?? null,
        ])->throw()->json();

        return [
            'provider' => $this->name(),
            'reference' => $response['data']['reference'],
            'redirect_url' => $response['data']['authorization_url'],
            'raw' => $response,
        ];
    }

    /**
     * {@inheritDoc}
     *
     * @throws ConnectionException|RequestException
     */
    public function verify(string $reference, array $context = []): array
    {
        $response = $this->client()->get("/transaction/verify/{$reference}")->throw()->json();

        return [
            'status' => $response['data']['status'] ?? 'unknown',
            'raw' => $response,
        ];
    }

    /**
     * {@inheritDoc}
     *
     * @throws ConnectionException
     * @throws RequestException
     */
    public function refund(string $reference, ?int $amountInMinor = null, ?string $reason = null): array
    {
        return $this->client()->post('/refund', array_filter([
            'transaction' => $reference,
            'amount' => $amountInMinor,
            'merchant_note' => $reason,
        ]))->throw()->json();
    }

    /**
     * {@inheritDoc}
     */
    public function parseWebhook(string $rawBody, array $headers): array
    {
        $signature = $headers['x-paystack-signature'][0] ?? ($headers['X-Paystack-Signature'][0] ?? '');
        $expected = hash_hmac('sha512', $rawBody, $this->secret);

        if (! hash_equals($expected, $signature)) {
            throw new RuntimeException('Invalid Paystack webhook signature.');
        }

        return json_decode($rawBody, true) ?? [];
    }

    /**
     * {@inheritDoc}
     */
    public function name(): string
    {
        return 'paystack';
    }
}
