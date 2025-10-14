<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        $admin = User::create([
            'name' => 'Amtar Admin',
            'email' => 'admin@amtar.om',
            'password' => Hash::make('password'),
            'phone' => '+968 1234 5678',
            'is_active' => true,
        ]);

        // Assign Administrator role
        $adminRole = Role::where('slug', 'administrator')->first();
        if ($adminRole) {
            $admin->roles()->attach($adminRole->id);
        }

        // Create Project Manager User
        $pm = User::create([
            'name' => 'Project Manager',
            'email' => 'pm@amtar.om',
            'password' => Hash::make('password'),
            'phone' => '+968 2345 6789',
            'is_active' => true,
        ]);

        $pmRole = Role::where('slug', 'project-manager')->first();
        if ($pmRole) {
            $pm->roles()->attach($pmRole->id);
        }

        // Create Engineer User
        $engineer = User::create([
            'name' => 'Senior Engineer',
            'email' => 'engineer@amtar.om',
            'password' => Hash::make('password'),
            'phone' => '+968 3456 7890',
            'is_active' => true,
        ]);

        $engineerRole = Role::where('slug', 'engineer')->first();
        if ($engineerRole) {
            $engineer->roles()->attach($engineerRole->id);
        }

        // Create Accountant User
        $accountant = User::create([
            'name' => 'Chief Accountant',
            'email' => 'accountant@amtar.om',
            'password' => Hash::make('password'),
            'phone' => '+968 4567 8901',
            'is_active' => true,
        ]);

        $accountantRole = Role::where('slug', 'accountant')->first();
        if ($accountantRole) {
            $accountant->roles()->attach($accountantRole->id);
        }
    }
}
