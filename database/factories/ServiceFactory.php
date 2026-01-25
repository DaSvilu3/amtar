<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\ServiceStage;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceFactory extends Factory
{
    protected $model = Service::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'name_en' => fn (array $attributes) => $attributes['name'],
            'description' => fake()->paragraph(),
            'description_en' => fn (array $attributes) => $attributes['description'],
            'service_stage_id' => ServiceStage::factory(),
            'unit_price' => fake()->numberBetween(1000, 50000),
            'unit' => fake()->randomElement(['sqm', 'item', 'lumpsum', 'hour']),
            'estimated_duration_days' => fake()->numberBetween(5, 60),
            'is_active' => true,
            'sort_order' => fake()->numberBetween(1, 100),
        ];
    }
}
