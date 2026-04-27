<?php

namespace App\Traits;

use App\Models\Tenant\Setting;
use Illuminate\Support\Facades\Config;
use RuntimeException;

/**
 * Trait ResolvesTenantPaymentConfig
 * Dynamically fetches payment credentials from the tenant's settings
 * and injects them into the current application environment.
 */
trait ResolvesTenantPaymentConfig
{
    /**
     * Resolves the payment credentials based on the test_mode flag,
     * and dynamically sets the Laravel config (putEnv equivalent).
     *
     * @param string $provider
     * @return array
     */
    protected function putEnv(string $provider): array
    {
        // Fetch global tenant settings
        $settings = Setting::query()->first();
        $providers = $settings?->payment_providers ?? [];

        $config = $providers[$provider] ?? null;

        if (!$config) {
            throw new RuntimeException("Payment provider [{$provider}] is not configured in tenant settings.");
        }

        if (!($config['enabled'] ?? false)) {
            throw new RuntimeException("Payment provider [{$provider}] is currently disabled.");
        }

        // Determine which keys to use based on the test_mode flag
        $mode = ($config['test_mode'] ?? true) ? 'test' : 'live';
        $credentials = $config[$mode] ?? [];

        // Dynamically set Laravel config (This acts as our "putEnv" for external packages)
        if ($provider === 'stripe') {
            Config::set('services.stripe.key', $credentials['public_key'] ?? '');
            Config::set('services.stripe.secret', $credentials['secret_key'] ?? '');
            Config::set('services.stripe.webhook_secret', $credentials['webhook_secret'] ?? '');
        } elseif ($provider === 'paystack') {
            Config::set('services.paystack.public_key', $credentials['public_key'] ?? '');
            Config::set('services.paystack.secret', $credentials['secret_key'] ?? '');
        }

        return $credentials;
    }
}
