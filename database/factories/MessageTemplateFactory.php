<?php

namespace Database\Factories;

use App\Models\MessageTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageTemplateFactory extends Factory
{
    protected $model = MessageTemplate::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'slug' => fn (array $attributes) => \Illuminate\Support\Str::slug($attributes['name']),
            'content' => fake()->sentence(),
            'type' => fake()->randomElement(['sms', 'whatsapp']),
            'variables' => ['project_name', 'task_title', 'deadline'],
            'is_active' => true,
        ];
    }

    public function sms(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'sms',
            'content' => fake()->text(160), // SMS character limit
        ]);
    }

    public function whatsapp(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'whatsapp',
        ]);
    }
}
