<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run seeders in order (roles first, then users, then settings and integrations)
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            SettingSeeder::class,
            IntegrationSeeder::class,
        ]);
    }
}
