<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Run the cleanup command across all tenant databases every night at 3:00 AM
Schedule::command('tenants:run app:clean-expired-carts')->dailyAt('03:00');

// Notify tenants whose plan expires within the next 3 days, every morning at 8:00 AM
Schedule::command('app:notify-expiring-subscriptions')->dailyAt('08:00');
