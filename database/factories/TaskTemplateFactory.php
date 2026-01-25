<?php

namespace Database\Factories;

use App\Models\TaskTemplate;
use App\Models\Service;
use App\Models\ServiceStage;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskTemplateFactory extends Factory
{
    protected $model = TaskTemplate::class;

    public function definition(): array
    {
        return [
            'name' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'service_id' => Service::factory(),
            'service_stage_id' => ServiceStage::factory(),
            'estimated_hours' => fake()->numberBetween(4, 80),
            'priority' => fake()->randomElement(['low', 'normal', 'high', 'urgent']),
            'required_skills' => fake()->optional()->randomElements([1, 2, 3, 4, 5], fake()->numberBetween(1, 3)),
            'sort_order' => fake()->numberBetween(1, 100),
            'is_active' => true,
        ];
    }

    public function highPriority(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => 'high',
        ]);
    }

    public function urgent(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => 'urgent',
        ]);
    }
}
