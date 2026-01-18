<?php

namespace Database\Factories;

use App\Models\Dish;
use App\Models\DishCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Dish>
 */
class DishFactory extends Factory
{
    protected $model = Dish::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $price = fake()->randomFloat(2, 100, 1000);
        $hasDiscount = fake()->boolean(30);

        return [
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(10),
            'price' => $price,
            'discount_price' => $hasDiscount ? fake()->randomFloat(2, 50, $price - 10) : null,
            'dish_category_id' => DishCategory::factory(),
            'image' => null,
            'weight_volume' => fake()->randomElement(['250 г', '350 г', '500 г', '300 мл', '400 мл']),
            'calories' => fake()->numberBetween(50, 800),
            'proteins' => fake()->randomFloat(2, 1, 50),
            'fats' => fake()->randomFloat(2, 1, 40),
            'carbohydrates' => fake()->randomFloat(2, 1, 100),
            'fiber' => fake()->randomFloat(2, 0, 10),
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }

    /**
     * Indicate that the dish has a discount.
     */
    public function withDiscount(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'discount_price' => fake()->randomFloat(2, 50, $attributes['price'] - 10),
            ];
        });
    }

    /**
     * Indicate that the dish has no discount.
     */
    public function withoutDiscount(): static
    {
        return $this->state(fn (array $attributes) => [
            'discount_price' => null,
        ]);
    }
}
