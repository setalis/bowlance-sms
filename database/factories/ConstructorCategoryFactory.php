<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ConstructorCategory>
 */
class ConstructorCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'name_ru' => fake('ru_RU')->words(2, true),
            'name_ka' => fake()->words(2, true),
            'sort_order' => fake()->numberBetween(1, 100),
        ];
    }
}
