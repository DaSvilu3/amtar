<?php

namespace Database\Factories;

use App\Models\DocumentType;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentTypeFactory extends Factory
{
    protected $model = DocumentType::class;

    public function definition(): array
    {
        $types = [
            'Contract', 'Technical Drawing', 'Report', 'Specification', 'Calculation Sheet',
            'Site Photo', 'Permit', 'Approval Letter', 'Meeting Minutes', 'Invoice'
        ];

        return [
            'name' => fake()->unique()->randomElement($types),
            'description' => fake()->sentence(),
            'required_for_projects' => fake()->boolean(30),
            'is_active' => true,
        ];
    }
}
