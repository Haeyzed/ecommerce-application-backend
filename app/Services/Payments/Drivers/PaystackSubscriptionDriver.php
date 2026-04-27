<?php

namespace App\Services\Payments\Drivers;

use App\Contracts\Payments\SubscriptionGatewayInterface;
use App\Traits\ResolvesTenantPaymentConfig;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Class PaystackSubscriptionDriver
 * * Implementation of the SubscriptionGatewayInterface for Paystack.
 */
class PaystackSubscriptionDriver implements SubscriptionGatewayInterface
{
    use ResolvesTenantPaymentConfig;

    protected string $secret;

    protected string $baseUrl;

    /**
     * Create a new PaystackSubscriptionDriver instance.
     */
    public function __construct()
    {
        $credentials = $this->putEnv('paystack');
        $this->secret = $credentials['secret_key'] ?? '';
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
     * @throws ConnectionException
     * @throws RequestException
     */
    public function ensureCustomer(array $owner): string
    {
        if (! empty($owner['provider_customer_id'])) {
            return $owner['provider_customer_id'];
        }

        $response = $this->client()->post('/customer', [
            'email' => $owner['email'],
            'first_name' => $owner['first_name'] ?? null,
            'last_name' => $owner['last_name'] ?? null,
            'metadata' => ['tenant_id' => $owner['tenant_id'] ?? null],
        ])->throw()->json();

        return $response['data']['customer_code'];
    }

    /**
     * {@inheritDoc}
     *
     * @throws ConnectionException
     * @throws RequestException
     */
    public function subscribe(string $customerId, string $planCode, array $opts = []): array
    {
        return $this->client()->post('/subscription', [
            'customer' => $customerId,
            'plan' => $planCode,
            'start_date' => $opts['start_date'] ?? null,
        ])->throw()->json();
    }

    /**
     * {@inheritDoc}
     *
     * @throws ConnectionException
     * @throws RequestException
     */
    public function cancel(string $subscriptionId, bool $atPeriodEnd = true): array
    {
        return $this->client()->post('/subscription/disable', [
            'code' => $subscriptionId,
            'token' => '', // Assuming token logic handles internal provider specifics
        ])->throw()->json();
    }

    /**
     * {@inheritDoc}
     *
     * @throws RequestException
     * @throws ConnectionException
     */
    public function resume(string $subscriptionId): array
    {
        return $this->client()->post('/subscription/enable', [
            'code' => $subscriptionId,
            'token' => '',
        ])->throw()->json();
    }

    /**
     * {@inheritDoc}
     *
     * @throws RequestException
     * @throws ConnectionException
     */
    public function invoice(string $customerId, int $amountInMinor, string $currency, string $description): array
    {
        return $this->client()->post('/paymentrequest', [
            'customer' => $customerId,
            'amount' => $amountInMinor,
            'currency' => strtoupper($currency),
            'description' => $description,
        ])->throw()->json();
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
