<?php

namespace Database\Factories;

use App\Enums\ConstructorType;
use App\Models\ConstructorCategory;
use App\Models\ConstructorProduct;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends Factory<ConstructorProduct>
 */
class ConstructorProductFactory extends Factory
{
    protected $model = ConstructorProduct::class;

    /**
     * @var list<string>
     */
    private const COMMERCIAL_KEYS = [
        'price',
        'weight_volume',
        'calories',
        'proteins',
        'fats',
        'carbohydrates',
        'fiber',
        'poster_modification_id',
    ];

    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'name_ru' => fake('ru_RU')->words(2, true),
            'name_ka' => fake()->words(2, true),
            'image' => null,
            'sort_order' => fake()->numberBetween(1, 100),
            'description' => fake()->sentence(),
            'description_ru' => fake('ru_RU')->sentence(),
            'description_ka' => fake()->sentence(),
        ];
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @return \Illuminate\Database\Eloquent\Collection<int, ConstructorProduct>|ConstructorProduct
     */
    public function create($attributes = [], ?Model $parent = null)
    {
        $posterBowl = null;
        $posterBreakfast = null;

        if (array_key_exists('poster_bowl_modification_id', $attributes)) {
            $posterBowl = $attributes['poster_bowl_modification_id'];
            unset($attributes['poster_bowl_modification_id']);
        }

        if (array_key_exists('poster_breakfast_modification_id', $attributes)) {
            $posterBreakfast = $attributes['poster_breakfast_modification_id'];
            unset($attributes['poster_breakfast_modification_id']);
        }

        $commercial = [];
        foreach (self::COMMERCIAL_KEYS as $key) {
            if (array_key_exists($key, $attributes)) {
                $commercial[$key] = $attributes[$key];
                unset($attributes[$key]);
            }
        }

        $result = parent::create($attributes, $parent);

        if ($result instanceof ConstructorProduct) {
            $this->syncFactoryVariants($result, $commercial, $posterBowl, $posterBreakfast);

            return $result->fresh(['variants', 'categories']);
        }

        $result->each(function (ConstructorProduct $product) use ($commercial, $posterBowl, $posterBreakfast): void {
            $this->syncFactoryVariants($product, $commercial, $posterBowl, $posterBreakfast);
        });

        return $result->fresh(['variants', 'categories']);
    }

    /**
     * @param  array<string, mixed>  $commercial
     */
    private function syncFactoryVariants(
        ConstructorProduct $product,
        array $commercial,
        mixed $posterBowl,
        mixed $posterBreakfast,
    ): void {
        $types = $product->categories()
            ->pluck('constructor_categories.type')
            ->unique()
            ->values();

        if ($types->isEmpty()) {
            $types = collect([ConstructorType::Bowl]);
        }

        if ($posterBreakfast !== null && ! $this->typesContain($types, ConstructorType::Breakfast)) {
            $types->push(ConstructorType::Breakfast);
        }

        if ($posterBowl !== null && ! $this->typesContain($types, ConstructorType::Bowl)) {
            $types->push(ConstructorType::Bowl);
        }

        $hasExplicitCommercial = $commercial !== [] || $posterBowl !== null || $posterBreakfast !== null;

        foreach ($types as $type) {
            $typeEnum = $type instanceof ConstructorType ? $type : ConstructorType::from($type);
            $existing = $product->variants()->where('type', $typeEnum)->first();

            if ($existing && ! $hasExplicitCommercial) {
                continue;
            }

            $payload = $commercial !== []
                ? $commercial
                : ($existing ? [] : [
                    'price' => fake()->randomFloat(2, 3, 15),
                    'weight_volume' => fake()->randomElement(['100 г', '150 г', '200 г']),
                    'calories' => fake()->numberBetween(50, 300),
                    'proteins' => fake()->randomFloat(1, 1, 30),
                    'fats' => fake()->randomFloat(1, 0, 20),
                    'carbohydrates' => fake()->randomFloat(1, 0, 50),
                    'fiber' => fake()->randomFloat(1, 0, 10),
                ]);

            if ($typeEnum === ConstructorType::Bowl && $posterBowl !== null) {
                $payload['poster_modification_id'] = $posterBowl;
            }

            if ($typeEnum === ConstructorType::Breakfast && $posterBreakfast !== null) {
                $payload['poster_modification_id'] = $posterBreakfast;
            }

            if ($payload === [] && $existing) {
                continue;
            }

            $product->variants()->updateOrCreate(
                ['type' => $typeEnum],
                $payload !== [] ? $payload : ['price' => 10]
            );
        }
    }

    /**
     * @param  \Illuminate\Support\Collection<int, ConstructorType|string>  $types
     */
    private function typesContain($types, ConstructorType $type): bool
    {
        return $types->contains(fn ($value) => $value === $type || $value === $type->value);
    }

    public function forCategories(ConstructorCategory ...$categories): static
    {
        return $this->afterCreating(function (ConstructorProduct $product) use ($categories): void {
            $product->categories()->sync(
                collect($categories)->pluck('id')->all()
            );
        });
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function withVariant(ConstructorType $type, array $attributes = []): static
    {
        return $this->afterCreating(function (ConstructorProduct $product) use ($type, $attributes): void {
            $product->variants()->updateOrCreate(
                ['type' => $type],
                $attributes
            );
        });
    }
}
