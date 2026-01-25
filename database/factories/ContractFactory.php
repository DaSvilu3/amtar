<?php

namespace Database\Factories;

use App\Models\Contract;
use App\Models\Client;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContractFactory extends Factory
{
    protected $model = Contract::class;

    public function definition(): array
    {
        $contractDate = fake()->dateTimeBetween('-1 year', 'now');

        return [
            'contract_number' => 'CNT-' . date('Y') . '-' . fake()->unique()->numberBetween(1000, 9999),
            'title' => fake()->sentence(5),
            'client_id' => Client::factory(),
            'project_id' => Project::factory(),
            'contract_date' => $contractDate,
            'start_date' => $contractDate,
            'end_date' => fake()->dateTimeBetween($contractDate, '+2 years'),
            'contract_value' => fake()->numberBetween(50000, 1000000),
            'payment_terms' => fake()->paragraph(),
            'scope_of_work' => fake()->paragraphs(3, true),
            'terms_and_conditions' => fake()->paragraphs(5, true),
            'status' => fake()->randomElement(['draft', 'pending_approval', 'active', 'completed', 'terminated']),
            'signed_by_client' => fake()->boolean(70),
            'signed_by_company' => fake()->boolean(80),
            'document_path' => fake()->optional()->filePath(),
            'notes' => fake()->optional()->paragraph(),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
            'signed_by_client' => false,
            'signed_by_company' => false,
        ]);
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'signed_by_client' => true,
            'signed_by_company' => true,
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'signed_by_client' => true,
            'signed_by_company' => true,
        ]);
    }
}
