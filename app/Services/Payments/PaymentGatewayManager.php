<?php

namespace App\Services\Payments;

use App\Contracts\Payments\PaymentGatewayInterface;
use App\Services\Payments\Drivers\PaystackDriver;
use App\Services\Payments\Drivers\StripeDriver;
use Illuminate\Support\Manager;

/**
 * Class PaymentGatewayManager
 * * Resolves the appropriate payment gateway driver (Stripe, Paystack, etc.) based on configuration.
 */
class PaymentGatewayManager extends Manager
{
    /**
     * Get the default payment driver name.
     */
    public function getDefaultDriver(): string
    {
        return $this->config->get('payments.default', 'stripe');
    }

    /**
     * Create an instance of the Stripe payment driver.
     */
    public function createStripeDriver(): PaymentGatewayInterface
    {
        return new StripeDriver;
    }

    /**
     * Create an instance of the Paystack payment driver.
     */
    public function createPaystackDriver(): PaymentGatewayInterface
    {
        return new PaystackDriver;
    }
}
