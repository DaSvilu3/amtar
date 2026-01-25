<?php

namespace Database\Factories;

use App\Models\MainService;
use Illuminate\Database\Eloquent\Factories\Factory;

class MainServiceFactory extends Factory
{
    protected $model = MainService::class;

    public function definition(): array
    {
        $services = [
            'Engineering Services', 'Design Services', 'Consulting Services',
            'Supervision Services', 'Project Management', 'Technical Studies'
        ];

        return [
            'name' => fake()->unique()->randomElement($services),
            'name_en' => fn (array $attributes) => $attributes['name'],
            'description' => fake()->paragraph(),
            'description_en' => fn (array $attributes) => $attributes['description'],
            'icon' => fake()->randomElement(['fa-building', 'fa-drafting-compass', 'fa-clipboard-check', 'fa-hard-hat']),
            'is_active' => true,
            'sort_order' => fake()->numberBetween(1, 10),
        ];
    }
}
