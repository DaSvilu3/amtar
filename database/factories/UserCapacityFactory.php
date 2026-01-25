<?php

namespace Database\Factories;

use App\Models\UserCapacity;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserCapacityFactory extends Factory
{
    protected $model = UserCapacity::class;

    public function definition(): array
    {
        $weekStart = now()->startOfWeek();

        return [
            'user_id' => User::factory(),
            'week_start_date' => $weekStart,
            'total_capacity_hours' => fake()->randomElement([40, 35, 30, 45]),
            'allocated_hours' => fake()->numberBetween(0, 40),
            'available_hours' => fn (array $attributes) =>
                $attributes['total_capacity_hours'] - $attributes['allocated_hours'],
            'utilization_percentage' => fn (array $attributes) =>
                round(($attributes['allocated_hours'] / $attributes['total_capacity_hours']) * 100, 2),
        ];
    }

    public function atCapacity(): static
    {
        return $this->state(fn (array $attributes) => [
            'allocated_hours' => $attributes['total_capacity_hours'],
            'available_hours' => 0,
            'utilization_percentage' => 100,
        ]);
    }

    public function underutilized(): static
    {
        return $this->state(fn (array $attributes) => [
            'allocated_hours' => fake()->numberBetween(5, 20),
            'available_hours' => $attributes['total_capacity_hours'] - fake()->numberBetween(5, 20),
            'utilization_percentage' => fake()->numberBetween(12, 50),
        ]);
    }
}
