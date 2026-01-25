<?php

namespace Database\Factories;

use App\Models\ServicePackage;
use App\Models\MainService;
use App\Models\SubService;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServicePackageFactory extends Factory
{
    protected $model = ServicePackage::class;

    public function definition(): array
    {
        $packages = [
            'Complete Engineering Package', 'Design Only Package', 'Supervision Package',
            'Consulting Package', 'Full Project Management', 'Basic Service Package'
        ];

        return [
            'name' => fake()->unique()->randomElement($packages),
            'name_en' => fn (array $attributes) => $attributes['name'],
            'description' => fake()->paragraph(),
            'description_en' => fn (array $attributes) => $attributes['description'],
            'main_service_id' => MainService::factory(),
            'sub_service_id' => SubService::factory(),
            'price' => fake()->numberBetween(10000, 500000),
            'duration_days' => fake()->numberBetween(30, 365),
            'is_active' => true,
            'sort_order' => fake()->numberBetween(1, 10),
        ];
    }
}
