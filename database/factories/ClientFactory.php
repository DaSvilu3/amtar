<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    protected $model = Client::class;

    public function definition(): array
    {
        $type = fake()->randomElement(['individual', 'company', 'government']);

        return [
            'name' => $type === 'individual' ? fake()->name() : fake()->company(),
            'type' => $type,
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'city' => fake()->city(),
            'country' => 'Oman',
            'tax_number' => fake()->optional()->numerify('TAX-#########'),
            'contact_person' => $type !== 'individual' ? fake()->name() : null,
            'contact_phone' => $type !== 'individual' ? fake()->phoneNumber() : null,
            'notes' => fake()->optional()->paragraph(),
            'is_active' => true,
        ];
    }

    public function individual(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'individual',
            'name' => fake()->name(),
            'contact_person' => null,
            'contact_phone' => null,
        ]);
    }

    public function company(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'company',
            'name' => fake()->company(),
            'contact_person' => fake()->name(),
            'contact_phone' => fake()->phoneNumber(),
        ]);
    }

    public function government(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'government',
            'name' => 'Ministry of ' . fake()->randomElement(['Housing', 'Infrastructure', 'Transport', 'Education']),
            'contact_person' => fake()->name(),
            'contact_phone' => fake()->phoneNumber(),
        ]);
    }
}
