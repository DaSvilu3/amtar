<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Client;
use App\Models\User;
use App\Models\MainService;
use App\Models\SubService;
use App\Models\ServicePackage;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-6 months', 'now');
        $endDate = fake()->dateTimeBetween($startDate, '+1 year');

        return [
            'name' => fake()->sentence(4),
            'project_number' => 'PRJ-' . date('Y') . '-' . fake()->unique()->numberBetween(1000, 9999),
            'client_id' => Client::factory(),
            'description' => fake()->paragraph(),
            'status' => fake()->randomElement(['planning', 'active', 'in_progress', 'on_hold', 'completed', 'cancelled']),
            'budget' => fake()->numberBetween(10000, 500000),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'actual_start_date' => fake()->optional()->dateTimeBetween($startDate, 'now'),
            'actual_end_date' => null,
            'project_manager_id' => User::factory(),
            'main_service_id' => MainService::factory(),
            'sub_service_id' => SubService::factory(),
            'service_package_id' => ServicePackage::factory(),
            'location' => fake()->address(),
            'progress' => fake()->numberBetween(0, 100),
        ];
    }

    public function planning(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'planning',
            'progress' => 0,
            'actual_start_date' => null,
        ]);
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'progress' => fake()->numberBetween(10, 50),
            'actual_start_date' => now()->subDays(fake()->numberBetween(1, 60)),
        ]);
    }

    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'in_progress',
            'progress' => fake()->numberBetween(30, 80),
            'actual_start_date' => now()->subDays(fake()->numberBetween(30, 90)),
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'progress' => 100,
            'actual_start_date' => now()->subDays(180),
            'actual_end_date' => now()->subDays(10),
        ]);
    }

    public function onHold(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'on_hold',
            'progress' => fake()->numberBetween(20, 60),
        ]);
    }
}
