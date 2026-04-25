<?php

namespace Database\Seeders\Central;

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class CentralRolesSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $matrix = config('roles.central');
        $guards = config('roles.guards', ['web', 'sanctum']);

        if (! is_array($matrix)) {
            throw new InvalidArgumentException('The config value [roles.central] must be an array. Check config/roles.php.');
        }

        if (! is_array($guards) || $guards === []) {
            throw new InvalidArgumentException('The config value [roles.guards] must be a non-empty array. Check config/roles.php.');
        }

        // Collect every concrete permission referenced by any role
        $allPerms = collect($matrix)->flatten()->unique()->reject(fn ($p) => $p === '*');

        // Expand patterns like "tenants.*" → "tenants.view","tenants.create","tenants.update","tenants.delete"
        $expanded = $allPerms->flatMap(function ($p) {
            return str_ends_with($p, '.*')
                ? collect(['view', 'create', 'update', 'delete'])->map(fn ($a) => str_replace('*', $a, $p))
                : [$p];
        })->unique()->values();

        foreach ($guards as $guard) {
            foreach ($expanded as $perm) {
                Permission::query()->firstOrCreate([
                    'name' => $perm,
                    'guard_name' => $guard,
                ]);
            }

            foreach ($matrix as $roleName => $perms) {
                if (! is_array($perms)) {
                    throw new InvalidArgumentException("Permissions for role [{$roleName}] must be an array.");
                }

                $role = Role::query()->firstOrCreate([
                    'name' => $roleName,
                    'guard_name' => $guard,
                ]);

                if (in_array('*', $perms, true)) {
                    $role->syncPermissions(
                        Permission::query()
                            ->where('guard_name', $guard)
                            ->get()
                    );
                } else {
                    $needed = collect($perms)
                        ->flatMap(fn ($p) => str_ends_with($p, '.*')
                            ? collect(['view', 'create', 'update', 'delete'])->map(fn ($a) => str_replace('*', $a, $p))
                            : [$p])
                        ->unique()
                        ->values();

                    $role->syncPermissions(
                        Permission::query()
                            ->whereIn('name', $needed)
                            ->where('guard_name', $guard)
                            ->get()
                    );
                }
            }
        }
    }
}
