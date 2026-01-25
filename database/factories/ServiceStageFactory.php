<?php

namespace Database\Factories;

use App\Models\ServiceStage;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceStageFactory extends Factory
{
    protected $model = ServiceStage::class;

    public function definition(): array
    {
        $stages = [
            'Concept Design', 'Preliminary Design', 'Detailed Design', 'Tender Documents',
            'Construction Documents', 'Shop Drawings', 'As-Built Drawings', 'Project Closeout'
        ];

        return [
            'name' => fake()->unique()->randomElement($stages),
            'name_en' => fn (array $attributes) => $attributes['name'],
            'description' => fake()->paragraph(),
            'description_en' => fn (array $attributes) => $attributes['description'],
            'sort_order' => fake()->numberBetween(1, 10),
            'is_active' => true,
        ];
    }
}
