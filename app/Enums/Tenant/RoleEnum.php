<?php

namespace App\Enums\Tenant;

enum RoleEnum: string
{
    case OWNER = 'owner';
    case MANAGER = 'manager';
    case STAFF = 'staff';
    case CUSTOMER = 'customer';

    public static function values(): array
    {
        return array_map(fn ($c) => $c->value, self::cases());
    }
}
