<?php

namespace Database\Seeders;

use App\Models\Central\User;
use Database\Seeders\Central\CentralTableSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CentralTableSeeder::class,
            CentralPermissionsSeeder::class, // Call the central permissions seeder
        ]);

        // Create a default super-admin user and assign the role
        $user = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'password' => bcrypt('password'), // Consider using a more secure default or .env
        ]);
        $user->assignRole('super-admin');

        // Create a regular admin user for testing central admin roles
        $adminUser = User::factory()->create([
            'name' => 'Central Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);
        $adminUser->assignRole('admin');
    }
}
