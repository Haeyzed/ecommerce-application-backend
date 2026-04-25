<?php

namespace App\Providers;

use App\Contracts\Payments\PaymentGatewayInterface;
use App\Services\Payments\PaymentGatewayManager;
use Illuminate\Support\ServiceProvider;

/**
 * Class PaymentServiceProvider
 * * Registers the payment gateway manager and binds the default driver interface.
 */
class PaymentServiceProvider extends ServiceProvider
{
    /**
     * Register services in the container.
     */
    public function register(): void
    {
        $this->app->singleton(PaymentGatewayManager::class);

        $this->app->bind(PaymentGatewayInterface::class, function ($app) {
            return $app->make(PaymentGatewayManager::class)->driver();
        });
    }
}
