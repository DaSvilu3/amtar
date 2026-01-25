<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{
    protected $model = Role::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement(['Administrator', 'Project Manager', 'Engineer', 'Senior Engineer', 'Junior Engineer']),
            'slug' => fn (array $attributes) => \Illuminate\Support\Str::slug($attributes['name']),
            'description' => fake()->sentence(),
            'permissions' => [
                'projects' => ['view', 'create', 'edit', 'delete'],
                'tasks' => ['view', 'create', 'edit'],
                'users' => ['view'],
            ],
            'is_active' => true,
        ];
    }

    public function administrator(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Administrator',
            'slug' => 'administrator',
            'permissions' => [
                'projects' => ['view', 'create', 'edit', 'delete'],
                'tasks' => ['view', 'create', 'edit', 'delete', 'assign'],
                'users' => ['view', 'create', 'edit', 'delete'],
                'settings' => ['view', 'edit'],
            ],
        ]);
    }

    public function projectManager(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Project Manager',
            'slug' => 'project-manager',
            'permissions' => [
                'projects' => ['view', 'create', 'edit'],
                'tasks' => ['view', 'create', 'edit', 'assign', 'review'],
                'users' => ['view'],
            ],
        ]);
    }

    public function engineer(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Engineer',
            'slug' => 'engineer',
            'permissions' => [
                'projects' => ['view'],
                'tasks' => ['view', 'update_progress'],
            ],
        ]);
    }
}
