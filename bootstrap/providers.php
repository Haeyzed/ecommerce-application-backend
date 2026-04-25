<?php

use App\Providers\AppServiceProvider;
use App\Providers\SubscriptionServiceProvider;
use App\Providers\TenancyServiceProvider;

return [
    AppServiceProvider::class,
    SubscriptionServiceProvider::class,
    TenancyServiceProvider::class,
];
