<?php

use App\Models\Central\User as CentralUser;
use App\Models\Tenant\User as TenantUser;

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | The default guard targets the central (SaaS) application. Tenant storefront
    | and admin APIs authenticate via the `tenant` guard in tenant services.
    |
    */

    'defaults' => [
        'guard' => env('AUTH_GUARD', 'web'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'users'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | `web` — central app users (`App\Models\Central\User`) on the `central` DB.
    | `tenant` — per-tenant users (`App\Models\Tenant\User`) on the tenant DB.
    |
    */

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
        'tenant' => [
            'driver' => 'session',
            'provider' => 'tenant_users',
        ],
        'admin' => [
            'driver' => 'sanctum',
            'provider' => 'tenant_users',
        ],
        'customer' => [
            'driver' => 'sanctum',
            'provider' => 'tenant_users',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    */

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => env('AUTH_MODEL', CentralUser::class),
        ],

        'tenant_users' => [
            'driver' => 'eloquent',
            'model' => TenantUser::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    */

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'connection' => 'central',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,
            'throttle' => 60,
        ],

        'tenant_users' => [
            'provider' => 'tenant_users',
            'table' => env('TENANT_AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    */

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];
