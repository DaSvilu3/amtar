<?php

namespace Database\Factories;

use App\Models\NotificationTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationTemplateFactory extends Factory
{
    protected $model = NotificationTemplate::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'slug' => fn (array $attributes) => \Illuminate\Support\Str::slug($attributes['name']),
            'title' => fake()->sentence(4),
            'body' => fake()->sentence(),
            'icon' => fake()->randomElement(['fa-check', 'fa-info', 'fa-warning', 'fa-tasks']),
            'color' => fake()->randomElement(['blue', 'green', 'yellow', 'red']),
            'event_type' => fake()->randomElement(['task_assigned', 'task_completed', 'project_created', 'milestone_reached']),
            'variables' => ['user_name', 'project_name', 'task_title'],
            'is_active' => true,
        ];
    }
}
