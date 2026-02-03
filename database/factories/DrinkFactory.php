<?php

namespace Database\Factories;

use App\Models\Drink;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Drink>
 */
class DrinkFactory extends Factory
{
    protected $model = Drink::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $price = fake()->randomFloat(2, 50, 500);
        $hasDiscount = fake()->boolean(30);

        return [
            'name' => fake()->words(2, true),
            'name_ru' => fake()->words(2, true),
            'name_ka' => null,
            'description' => fake()->sentence(8),
            'description_ru' => fake()->sentence(8),
            'description_ka' => null,
            'price' => $price,
            'discount_price' => $hasDiscount ? fake()->randomFloat(2, 30, $price - 10) : null,
            'image' => null,
            'volume' => fake()->randomElement(['250 мл', '330 мл', '500 мл', '1 л', '1.5 л']),
            'calories' => fake()->numberBetween(20, 400),
            'proteins' => fake()->randomFloat(2, 0, 5),
            'fats' => fake()->randomFloat(2, 0, 3),
            'carbohydrates' => fake()->randomFloat(2, 0, 50),
            'fiber' => fake()->randomFloat(2, 0, 2),
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }

    /**
     * Indicate that the drink has a discount.
     */
    public function withDiscount(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'discount_price' => fake()->randomFloat(2, 30, $attributes['price'] - 10),
            ];
        });
    }

    /**
     * Indicate that the drink has no discount.
     */
    public function withoutDiscount(): static
    {
        return $this->state(fn (array $attributes) => [
            'discount_price' => null,
        ]);
    }
}
