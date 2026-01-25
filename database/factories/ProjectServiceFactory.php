<?php

namespace Database\Factories;

use App\Models\ProjectService;
use App\Models\Project;
use App\Models\Service;
use App\Models\ServiceStage;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectServiceFactory extends Factory
{
    protected $model = ProjectService::class;

    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'service_id' => Service::factory(),
            'service_stage_id' => ServiceStage::factory(),
            'quantity' => fake()->numberBetween(1, 100),
            'unit_price' => fake()->numberBetween(1000, 50000),
            'total_price' => fn (array $attributes) => $attributes['quantity'] * $attributes['unit_price'],
            'description' => fake()->optional()->sentence(),
            'is_active' => true,
        ];
    }
}
