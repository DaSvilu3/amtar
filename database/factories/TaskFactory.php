<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\Project;
use App\Models\ProjectService;
use App\Models\Milestone;
use App\Models\TaskTemplate;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-2 months', '+1 month');
        $dueDate = fake()->dateTimeBetween($startDate, '+2 months');

        return [
            'project_id' => Project::factory(),
            'project_service_id' => ProjectService::factory(),
            'milestone_id' => fake()->optional()->randomElement([null, Milestone::factory()]),
            'task_template_id' => fake()->optional()->randomElement([null, TaskTemplate::factory()]),
            'assigned_to' => User::factory(),
            'reviewed_by' => fake()->optional()->randomElement([null, User::factory()]),
            'created_by' => User::factory(),
            'title' => fake()->sentence(5),
            'description' => fake()->paragraph(),
            'status' => fake()->randomElement(['pending', 'in_progress', 'review', 'completed', 'on_hold']),
            'priority' => fake()->randomElement(['low', 'normal', 'high', 'urgent']),
            'start_date' => $startDate,
            'due_date' => $dueDate,
            'completed_at' => null,
            'reviewed_at' => null,
            'review_notes' => null,
            'estimated_hours' => fake()->numberBetween(4, 80),
            'actual_hours' => fake()->optional()->numberBetween(2, 100),
            'progress' => fake()->numberBetween(0, 100),
            'requires_review' => fake()->boolean(60),
            'sort_order' => fake()->numberBetween(1, 100),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'progress' => 0,
            'assigned_to' => null,
        ]);
    }

    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'in_progress',
            'progress' => fake()->numberBetween(20, 70),
        ]);
    }

    public function review(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'review',
            'progress' => 100,
            'requires_review' => true,
            'reviewed_by' => User::factory(),
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'progress' => 100,
            'completed_at' => now(),
            'actual_hours' => fake()->numberBetween(4, 80),
        ]);
    }

    public function urgent(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => 'urgent',
        ]);
    }

    public function overdue(): static
    {
        return $this->state(fn (array $attributes) => [
            'due_date' => now()->subDays(fake()->numberBetween(1, 30)),
            'status' => 'in_progress',
        ]);
    }
}
