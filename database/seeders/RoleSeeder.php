<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Administrator',
                'slug' => 'administrator',
                'description' => 'Full system access with all permissions',
                'permissions' => [
                    'users' => ['create', 'read', 'update', 'delete'],
                    'roles' => ['create', 'read', 'update', 'delete'],
                    'settings' => ['create', 'read', 'update', 'delete'],
                    'files' => ['create', 'read', 'update', 'delete'],
                    'integrations' => ['create', 'read', 'update', 'delete'],
                    'notifications' => ['create', 'read', 'update', 'delete'],
                    'emails' => ['create', 'read', 'update', 'delete'],
                    'messages' => ['create', 'read', 'update', 'delete'],
                    'clients' => ['create', 'read', 'update', 'delete'],
                    'projects' => ['create', 'read', 'update', 'delete'],
                    'contracts' => ['create', 'read', 'update', 'delete'],
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Project Manager',
                'slug' => 'project-manager',
                'description' => 'Can manage projects, clients, and contracts',
                'permissions' => [
                    'files' => ['create', 'read', 'update'],
                    'clients' => ['create', 'read', 'update'],
                    'projects' => ['create', 'read', 'update', 'delete'],
                    'contracts' => ['create', 'read', 'update'],
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Engineer',
                'slug' => 'engineer',
                'description' => 'Can view projects and update tasks',
                'permissions' => [
                    'files' => ['read', 'create'],
                    'clients' => ['read'],
                    'projects' => ['read', 'update'],
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Accountant',
                'slug' => 'accountant',
                'description' => 'Can manage contracts and view financial data',
                'permissions' => [
                    'clients' => ['read'],
                    'projects' => ['read'],
                    'contracts' => ['create', 'read', 'update', 'delete'],
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Viewer',
                'slug' => 'viewer',
                'description' => 'Read-only access to most areas',
                'permissions' => [
                    'files' => ['read'],
                    'clients' => ['read'],
                    'projects' => ['read'],
                    'contracts' => ['read'],
                ],
                'is_active' => true,
            ],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
