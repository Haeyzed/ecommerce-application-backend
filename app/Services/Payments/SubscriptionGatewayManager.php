<?php

namespace App\Services\Payments;

use App\Contracts\Payments\SubscriptionGatewayInterface;
use App\Services\Payments\Drivers\PaystackSubscriptionDriver;
use App\Services\Payments\Drivers\StripeSubscriptionDriver;
use Illuminate\Support\Manager;

/**
 * Class SubscriptionGatewayManager
 * * Resolves the appropriate subscription gateway driver (Stripe, Paystack, etc.) based on configuration.
 */
class SubscriptionGatewayManager extends Manager
{
    /**
     * Get the default subscription driver name.
     */
    public function getDefaultDriver(): string
    {
        return $this->config->get('payments.default', 'stripe');
    }

    /**
     * Create an instance of the Stripe subscription driver.
     */
    public function createStripeDriver(): SubscriptionGatewayInterface
    {
        return new StripeSubscriptionDriver;
    }

    /**
     * Create an instance of the Paystack subscription driver.
     */
    public function createPaystackDriver(): SubscriptionGatewayInterface
    {
        return new PaystackSubscriptionDriver;
    }
}
