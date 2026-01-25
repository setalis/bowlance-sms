<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PhoneVerification>
 */
class PhoneVerificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'phone' => '+995'.fake()->numerify('#########'),
            'request_id' => fake()->uuid(),
            'code' => null,
            'verified' => false,
            'verified_at' => null,
            'expires_at' => now()->addMinutes(5),
            'attempts' => 0,
        ];
    }

    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'verified' => true,
            'verified_at' => now(),
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => now()->subMinutes(1),
        ]);
    }

    public function withMaxAttempts(): static
    {
        return $this->state(fn (array $attributes) => [
            'attempts' => 3,
        ]);
    }
}
