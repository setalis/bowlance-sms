<?php

namespace App\Services;

use App\Enums\ConstructorType;
use App\Enums\OrderMode;
use App\Models\ConstructorProduct;
use App\Models\Dish;
use App\Models\Drink;
use Illuminate\Validation\ValidationException;

class OrderItemPriceResolver
{
    public function __construct(protected ProductPriceService $prices) {}

    /**
     * @param  array<int, array<string, mixed>>  $items
     * @return array<int, array<string, mixed>>
     */
    public function priceItems(array $items, OrderMode $mode): array
    {
        $priced = [];

        foreach ($items as $index => $item) {
            $priced[] = $this->priceItem($item, $mode, (int) $index);
        }

        return $priced;
    }

    /**
     * @param  array<string, mixed>  $item
     * @return array<string, mixed>
     */
    public function priceItem(array $item, OrderMode $mode, int $index): array
    {
        return match ($item['type'] ?? null) {
            'dish' => $this->priceDish($item, $mode, $index),
            'drink' => $this->priceDrink($item, $mode, $index),
            'bowl', 'breakfast' => $this->priceConstructor($item, $mode),
            default => throw ValidationException::withMessages([
                "items.$index.type" => 'Неизвестный тип позиции.',
            ]),
        };
    }

    /**
     * @param  array<string, mixed>  $item
     * @return array<string, mixed>
     */
    protected function priceDish(array $item, OrderMode $mode, int $index): array
    {
        $dish = Dish::query()->with('addons')->find($item['id'] ?? null);

        if (! $dish) {
            throw ValidationException::withMessages([
                "items.$index.id" => 'Блюдо не найдено.',
            ]);
        }

        $catalogPrice = $this->prices->catalogAmount($dish->price, $dish->discount_price);
        $addonsData = [];
        $addonsTotal = 0.0;
        $addonCalories = 0;
        $addonProteins = 0.0;
        $addonFats = 0.0;
        $addonCarbs = 0.0;

        foreach ($item['addons'] ?? [] as $addonInput) {
            if (! is_array($addonInput)) {
                continue;
            }

            $attached = $dish->addons->firstWhere('id', (int) ($addonInput['id'] ?? 0));

            if (! $attached) {
                continue;
            }

            $quantity = max(1, (int) ($addonInput['quantity'] ?? 1));
            $addonCatalog = $this->prices->catalogAmount($attached->pivot->price ?? $attached->price);
            $addonPrice = $this->prices->forMode($addonCatalog, $mode);
            $addonsTotal += $addonPrice * $quantity;
            $addonCalories += (int) ($attached->calories ?? 0) * $quantity;
            $addonProteins += (float) ($attached->proteins ?? 0) * $quantity;
            $addonFats += (float) ($attached->fats ?? 0) * $quantity;
            $addonCarbs += (float) ($attached->carbohydrates ?? 0) * $quantity;

            $addonsData[] = [
                'id' => $attached->id,
                'name' => $attached->name,
                'catalogPrice' => $addonCatalog,
                'price' => $addonPrice,
                'quantity' => $quantity,
                'calories' => $attached->calories,
                'proteins' => $attached->proteins,
                'fats' => $attached->fats,
                'carbs' => $attached->carbohydrates,
            ];
        }

        $item['catalogPrice'] = $catalogPrice;
        $item['price'] = round($this->prices->forMode($catalogPrice, $mode) + $addonsTotal, 2);
        $item['addons'] = $addonsData !== [] ? $addonsData : null;
        $item['name'] = $dish->name;
        $item['calories'] = (int) ($dish->calories ?? 0) + $addonCalories;
        $item['proteins'] = (float) ($dish->proteins ?? 0) + $addonProteins;
        $item['fats'] = (float) ($dish->fats ?? 0) + $addonFats;
        $item['carbs'] = (float) ($dish->carbohydrates ?? 0) + $addonCarbs;

        return $item;
    }

    /**
     * @param  array<string, mixed>  $item
     * @return array<string, mixed>
     */
    protected function priceDrink(array $item, OrderMode $mode, int $index): array
    {
        $drink = Drink::query()->find($item['id'] ?? null);

        if (! $drink) {
            throw ValidationException::withMessages([
                "items.$index.id" => 'Напиток не найден.',
            ]);
        }

        $catalogPrice = $this->prices->catalogAmount($drink->price, $drink->discount_price);
        $item['catalogPrice'] = $catalogPrice;
        $item['price'] = $this->prices->forMode($catalogPrice, $mode);

        return $item;
    }

    /**
     * @param  array<string, mixed>  $item
     * @return array<string, mixed>
     */
    protected function priceConstructor(array $item, OrderMode $mode): array
    {
        $type = ($item['type'] ?? null) === 'breakfast'
            ? ConstructorType::Breakfast
            : ConstructorType::Bowl;

        $products = [];
        $line = 0.0;
        $resolved = 0;

        foreach ($item['products'] ?? [] as $productInput) {
            if (! is_array($productInput) || ! isset($productInput['id']) || ! is_numeric($productInput['id'])) {
                continue;
            }

            $product = ConstructorProduct::query()->with('variants')->find((int) $productInput['id']);
            $variant = $product?->variantFor($type);

            if (! $product || $variant === null) {
                continue;
            }

            $quantity = max(1, (int) ($productInput['quantity'] ?? 1));
            $catalogPrice = $this->prices->catalogAmount($variant->price);
            $modePrice = $this->prices->forMode($catalogPrice, $mode);
            $line += $modePrice * $quantity;
            $resolved++;

            $productInput['name'] = $product->name;
            $productInput['catalogPrice'] = $catalogPrice;
            $productInput['price'] = $modePrice;
            $productInput['quantity'] = $quantity;
            $products[] = $productInput;
        }

        if ($resolved > 0) {
            $item['products'] = $products;
            $item['catalogPrice'] = round($line, 2);
            $item['price'] = round($line, 2);

            return $item;
        }

        $catalogPrice = round((float) ($item['price'] ?? 0), 2);
        $item['catalogPrice'] = $catalogPrice;
        $item['price'] = $this->prices->forMode($catalogPrice, $mode);

        return $item;
    }
}
