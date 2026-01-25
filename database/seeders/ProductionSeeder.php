<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * ProductionSeeder - Safe seeder for production deployment
 *
 * This seeder only includes essential data required for the system to function:
 * - Roles and permissions (required for user management)
 * - System settings (application configuration)
 * - Integration templates (without credentials)
 * - Service catalog (company service offerings)
 * - Document types (Oman-specific document classifications)
 *
 * DO NOT include seeders with dummy data like:
 * - UserSeeder (contains hardcoded passwords)
 * - ClientSeeder (dummy client data)
 * - ProjectSeeder (dummy project data)
 * - MilestoneSeeder (depends on dummy projects)
 * - TaskSeeder (depends on dummy projects)
 * - ContractSeeder (depends on dummy projects)
 *
 * Usage:
 *   php artisan db:seed --class=ProductionSeeder
 *
 * After running this seeder, create an admin user using:
 *   php artisan admin:create
 */
class ProductionSeeder extends Seeder
{
    /**
     * Run the database seeds for production.
     */
    public function run(): void
    {
        $this->command->info('Running Production Seeder...');
        $this->command->newLine();

        // 1. Roles and Permissions - Required for user authentication/authorization
        $this->command->info('Seeding roles and permissions...');
        $this->call(RoleSeeder::class);

        // 2. System Settings - Application configuration
        $this->command->info('Seeding system settings...');
        $this->call(SettingSeeder::class);

        // 3. Service Catalog - Company service offerings
        $this->command->info('Seeding service catalog...');
        $this->call(ServiceSeeder::class);

        // 5. Document Types - Oman-specific document classifications
        $this->command->info('Seeding document types...');
        $this->call(DocumentTypeSeeder::class);

        // 6. Task Templates - Templates for auto-generating project tasks (also creates skills)
        $this->command->info('Seeding task templates...');
        $this->call(TaskTemplateSeeder::class);

        // 7. Employees - AMTAR team members with roles and skills
        $this->command->info('Seeding employees...');
        $this->call(EmployeeSeeder::class);

        $this->command->newLine();
        $this->command->info('Production seeding completed successfully!');
        $this->command->newLine();
        $this->command->info('Default employee password: Amtar2024!');
        $this->command->warn('Please change passwords after first login.');
    }
}
