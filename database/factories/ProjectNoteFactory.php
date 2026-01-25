<?php

namespace Database\Factories;

use App\Models\ProjectNote;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectNoteFactory extends Factory
{
    protected $model = ProjectNote::class;

    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'user_id' => User::factory(),
            'type' => fake()->randomElement(['comment', 'reminder', 'calendar_event']),
            'content' => fake()->paragraph(),
            'is_pinned' => fake()->boolean(20),
            'event_date' => fake()->optional()->dateTimeBetween('now', '+6 months'),
            'event_time' => fake()->optional()->time(),
            'priority' => fake()->randomElement(['low', 'normal', 'high']),
        ];
    }

    public function comment(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'comment',
            'event_date' => null,
            'event_time' => null,
        ]);
    }

    public function reminder(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'reminder',
            'event_date' => fake()->dateTimeBetween('now', '+1 month'),
        ]);
    }

    public function calendarEvent(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'calendar_event',
            'event_date' => fake()->dateTimeBetween('now', '+3 months'),
            'event_time' => fake()->time(),
        ]);
    }

    public function pinned(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_pinned' => true,
        ]);
    }
}
