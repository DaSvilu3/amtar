<?php

namespace Database\Factories;

use App\Models\SubService;
use App\Models\MainService;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubServiceFactory extends Factory
{
    protected $model = SubService::class;

    public function definition(): array
    {
        $subServices = [
            'Structural Design', 'MEP Design', 'Architecture Design', 'Interior Design',
            'Landscape Design', 'Site Supervision', 'Quality Control', 'Cost Estimation'
        ];

        return [
            'main_service_id' => MainService::factory(),
            'name' => fake()->unique()->randomElement($subServices),
            'name_en' => fn (array $attributes) => $attributes['name'],
            'description' => fake()->paragraph(),
            'description_en' => fn (array $attributes) => $attributes['description'],
            'is_active' => true,
            'sort_order' => fake()->numberBetween(1, 10),
        ];
    }
}
