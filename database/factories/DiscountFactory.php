<?php

namespace Database\Factories;

use App\Enums\DiscountScope;
use App\Enums\DiscountType;
use App\Models\Discount;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Discount>
 */
class DiscountFactory extends Factory
{
    protected $model = Discount::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Скидка за самовывоз',
            'size' => fake()->randomFloat(2, 5, 15),
            'type' => DiscountType::Percent,
            'scope' => DiscountScope::Pickup,
            'min_cart_total' => null,
            'is_active' => true,
        ];
    }

    public function percent(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => DiscountType::Percent,
            'size' => fake()->numberBetween(5, 20),
        ]);
    }

    public function amount(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => DiscountType::Amount,
            'size' => fake()->randomFloat(2, 1, 5),
        ]);
    }

    public function cartTotal(float $minCartTotal = 100): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Скидка по сумме корзины',
            'scope' => DiscountScope::CartTotal,
            'min_cart_total' => $minCartTotal,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => ['is_active' => false]);
    }
}
