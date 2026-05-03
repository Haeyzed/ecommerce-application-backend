<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class CentralPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Define Central Permissions
        $permissions = [
            // Plans
            'view plans', 'create plans', 'update plans', 'delete plans',
            // Users (Central)
            'view central users', 'create central users', 'update central users', 'delete central users', 'assign central roles',
            // Tenants
            'view tenants', 'create tenants', 'update tenants', 'delete tenants', 'manage tenant domains',
            // Invoices
            'view invoices', 'create invoices', 'update invoices', 'delete invoices',
            // Settings (Central)
            'view central settings', 'update central settings',
            // Audit Logs
            'view audit logs', 'delete audit logs',
            // Subscriptions
            'view subscriptions', 'create subscriptions', 'update subscriptions', 'cancel subscriptions', 'activate subscriptions', 'invoice subscriptions',
            // Notification Channels
            'view notification channels', 'create notification channels', 'update notification channels', 'delete notification channels',
            // Notification Templates
            'view notification templates', 'create notification templates', 'update notification templates', 'delete notification templates',
            // Notification Preferences
            'view notification preferences', 'update notification preferences',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create Roles and Assign Permissions
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        // Super Admin gets all permissions
        $superAdminRole->givePermissionTo(Permission::where('guard_name', 'web')->get());

        // Admin gets a subset of permissions
        $adminRole->givePermissionTo([
            'view plans', 'view central users', 'update central users',
            'view tenants', 'update tenants', 'manage tenant domains',
            'view invoices',
            'view central settings', 'update central settings',
            'view audit logs',
            'view subscriptions', 'update subscriptions', 'cancel subscriptions', 'activate subscriptions', 'invoice subscriptions',
            'view notification channels', 'view notification templates', 'view notification preferences', 'update notification preferences',
        ]);

        // You might want to assign a default role to existing central users here
        // Example: App\Models\Central\User::whereDoesntHave('roles')->first()?->assignRole('admin');
    }
}
