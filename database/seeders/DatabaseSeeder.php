<?php

namespace Database\Seeders;

use App\Models\Central\User;
use Database\Seeders\Central\CentralRolesSeeder;
use Database\Seeders\Central\PlanTableSeed;
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
        $this->call(PlanTableSeed::class);
        $this->call(CentralRolesSeeder::class);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
