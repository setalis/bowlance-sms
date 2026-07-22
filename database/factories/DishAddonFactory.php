<?php

namespace Database\Factories;

use App\Models\DishAddon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DishAddon>
 */
class DishAddonFactory extends Factory
{
    protected $model = DishAddon::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'name_ru' => fake('ru_RU')->words(2, true),
            'name_ka' => fake()->words(2, true),
            'price' => fake()->randomFloat(2, 1, 15),
            'sort_order' => fake()->numberBetween(0, 100),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
