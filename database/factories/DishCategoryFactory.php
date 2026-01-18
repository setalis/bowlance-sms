<?php

namespace Database\Factories;

use App\Models\DishCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DishCategory>
 */
class DishCategoryFactory extends Factory
{
    protected $model = DishCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'slug' => fake()->unique()->slug(),
            'description' => fake()->sentence(),
            'image' => null,
            'is_active' => fake()->boolean(80),
            'sort' => fake()->numberBetween(0, 100),
            'meta_title' => fake()->sentence(),
            'meta_description' => fake()->text(160),
            'meta_keywords' => implode(', ', fake()->words(5)),
            'meta_image' => null,
            'meta_url' => null,
            'meta_type' => 'website',
        ];
    }

    /**
     * Indicate that the category is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the category is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
