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
        // Run seeders in order (respecting foreign key constraints)
        $this->call([
            // Core system data
            RoleSeeder::class,
            UserSeeder::class,
            SettingSeeder::class,
            ServiceSeeder::class,        // Seed all services, packages, stages
            DocumentTypeSeeder::class,

            // Templates for notifications
            EmailTemplateSeeder::class,
            NotificationTemplateSeeder::class,
            MessageTemplateSeeder::class,

            // Business data (depends on core data)
            ClientSeeder::class,         // Clients
            ProjectSeeder::class,        // Projects (depends on clients, users, services)
            MilestoneSeeder::class,      // Milestones (depends on projects)
            TaskSeeder::class,           // Tasks (depends on projects, milestones, users)
            ContractSeeder::class,       // Contracts (depends on projects, clients)
        ]);
    }
}
