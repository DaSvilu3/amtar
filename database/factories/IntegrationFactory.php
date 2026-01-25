<?php

namespace Database\Factories;

use App\Models\Integration;
use Illuminate\Database\Eloquent\Factories\Factory;

class IntegrationFactory extends Factory
{
    protected $model = Integration::class;

    public function definition(): array
    {
        $type = fake()->randomElement(['email', 'sms', 'whatsapp']);

        return [
            'name' => ucfirst($type) . ' Integration',
            'type' => $type,
            'config' => match($type) {
                'email' => [
                    'smtp_host' => 'smtp.gmail.com',
                    'smtp_port' => 587,
                    'smtp_username' => fake()->email(),
                    'smtp_password' => fake()->password(),
                    'from_email' => fake()->email(),
                    'from_name' => fake()->company(),
                ],
                'sms' => [
                    'account_sid' => fake()->uuid(),
                    'auth_token' => fake()->sha256(),
                    'from_number' => fake()->phoneNumber(),
                ],
                'whatsapp' => [
                    'account_sid' => fake()->uuid(),
                    'auth_token' => fake()->sha256(),
                    'from_number' => fake()->phoneNumber(),
                ],
            },
            'is_active' => fake()->boolean(70),
            'last_sync_at' => fake()->optional()->dateTimeBetween('-1 week', 'now'),
        ];
    }

    public function email(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Email Integration',
            'type' => 'email',
            'config' => [
                'smtp_host' => 'smtp.gmail.com',
                'smtp_port' => 587,
                'smtp_username' => fake()->email(),
                'smtp_password' => fake()->password(),
                'from_email' => fake()->email(),
                'from_name' => fake()->company(),
            ],
        ]);
    }

    public function sms(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'SMS Integration',
            'type' => 'sms',
            'config' => [
                'account_sid' => fake()->uuid(),
                'auth_token' => fake()->sha256(),
                'from_number' => fake()->phoneNumber(),
            ],
        ]);
    }

    public function whatsapp(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'WhatsApp Integration',
            'type' => 'whatsapp',
            'config' => [
                'account_sid' => fake()->uuid(),
                'auth_token' => fake()->sha256(),
                'from_number' => fake()->phoneNumber(),
            ],
        ]);
    }
}
