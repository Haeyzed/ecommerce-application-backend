<?php

namespace App\Providers;

use App\Contracts\Payments\SubscriptionGatewayInterface;
use App\Services\Payments\SubscriptionGatewayManager;
use Illuminate\Support\ServiceProvider;

/**
 * Class SubscriptionServiceProvider
 * * Registers the subscription gateway manager and binds the default driver interface.
 */
class SubscriptionServiceProvider extends ServiceProvider
{
    /**
     * Register services in the container.
     */
    public function register(): void
    {
        $this->app->singleton(SubscriptionGatewayManager::class);

        $this->app->bind(SubscriptionGatewayInterface::class, function ($app) {
            return $app->make(SubscriptionGatewayManager::class)->driver();
        });
    }
}
