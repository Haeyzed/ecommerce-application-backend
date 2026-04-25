<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Support\Facades\App;
use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;
use Stancl\Tenancy\Tenancy;

/**
 * Sanctum token storage: central DB when off a tenant host; tenant DB when Stancl tenancy is active.
 */
class PersonalAccessToken extends SanctumPersonalAccessToken
{
    public function getConnectionName(): ?string
    {
        if (App::bound(Tenancy::class) && App::make(Tenancy::class)->initialized) {
            return (string) config('database.default');
        }

        return (string) config('tenancy.database.central_connection', 'central');
    }
}
