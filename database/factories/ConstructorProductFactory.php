<?php

namespace Database\Factories;

use App\Models\ConstructorCategory;
use App\Models\ConstructorProduct;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ConstructorProduct>
 */
class ConstructorProductFactory extends Factory
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
            'price' => fake()->randomFloat(2, 3, 15),
            'image' => null,
            'sort_order' => fake()->numberBetween(1, 100),
            'description' => fake()->sentence(),
            'description_ru' => fake('ru_RU')->sentence(),
            'description_ka' => fake()->sentence(),
            'weight_volume' => fake()->randomElement(['100 г', '150 г', '200 г', '250 г']),
            'calories' => fake()->numberBetween(50, 300),
            'proteins' => fake()->randomFloat(1, 1, 30),
            'fats' => fake()->randomFloat(1, 0, 20),
            'carbohydrates' => fake()->randomFloat(1, 0, 50),
            'fiber' => fake()->randomFloat(1, 0, 10),
        ];
    }

    public function forCategories(ConstructorCategory ...$categories): static
    {
        return $this->afterCreating(function (ConstructorProduct $product) use ($categories): void {
            $product->categories()->sync(
                collect($categories)->pluck('id')->all()
            );
        });
    }
}
