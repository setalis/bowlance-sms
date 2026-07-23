<?php

namespace Database\Factories;

use App\Enums\ConstructorType;
use App\Models\ConstructorProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ConstructorProductVariant>
 */
class ConstructorProductVariantFactory extends Factory
{
    protected $model = ConstructorProductVariant::class;

    public function definition(): array
    {
        return [
            'type' => ConstructorType::Bowl,
            'price' => fake()->randomFloat(2, 3, 15),
            'weight_volume' => fake()->randomElement(['100 г', '150 г', '200 г', '250 г']),
            'calories' => fake()->numberBetween(50, 300),
            'proteins' => fake()->randomFloat(1, 1, 30),
            'fats' => fake()->randomFloat(1, 0, 20),
            'carbohydrates' => fake()->randomFloat(1, 0, 50),
            'fiber' => fake()->randomFloat(1, 0, 10),
            'poster_modification_id' => null,
        ];
    }

    public function breakfast(): static
    {
        return $this->state(fn () => ['type' => ConstructorType::Breakfast]);
    }
}
