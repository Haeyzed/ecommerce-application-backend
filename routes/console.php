<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Run the cleanup command across all tenant databases every night at 3:00 AM
Schedule::command('tenants:run app:clean-expired-carts')->dailyAt('03:00');
