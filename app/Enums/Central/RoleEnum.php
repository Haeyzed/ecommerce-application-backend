<?php

namespace App\Enums\Central;

enum RoleEnum: string
{
    case SUPER_ADMIN = 'super-admin';
    case ADMIN = 'admin';
    case SUPPORT = 'support';

    public static function values(): array
    {
        return array_map(fn ($c) => $c->value, self::cases());
    }
}
