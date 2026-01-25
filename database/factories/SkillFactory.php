<?php

namespace Database\Factories;

use App\Models\Skill;
use Illuminate\Database\Eloquent\Factories\Factory;

class SkillFactory extends Factory
{
    protected $model = Skill::class;

    public function definition(): array
    {
        $skills = [
            'Structural Engineering', 'MEP Design', 'Architecture', 'AutoCAD', 'Revit',
            'Project Management', 'Site Supervision', 'Interior Design', 'Landscape Design',
            'Cost Estimation', 'Quality Control', 'Safety Management', '3D Modeling'
        ];

        return [
            'name' => fake()->unique()->randomElement($skills),
            'description' => fake()->sentence(),
            'category' => fake()->randomElement(['technical', 'management', 'design', 'software']),
            'is_active' => true,
        ];
    }
}
