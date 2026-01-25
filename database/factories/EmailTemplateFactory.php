<?php

namespace Database\Factories;

use App\Models\EmailTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmailTemplateFactory extends Factory
{
    protected $model = EmailTemplate::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'slug' => fn (array $attributes) => \Illuminate\Support\Str::slug($attributes['name']),
            'subject' => fake()->sentence(),
            'body' => fake()->paragraphs(3, true),
            'variables' => ['project_name', 'client_name', 'due_date', 'task_title'],
            'is_active' => true,
        ];
    }
}
