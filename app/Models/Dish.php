<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Dish extends Model
{
    /** @use HasFactory<\Database\Factories\DishFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'name_ru',
        'name_ka',
        'description',
        'description_ru',
        'description_ka',
        'price',
        'discount_price',
        'dish_category_id',
        'image',
        'weight_volume',
        'calories',
        'proteins',
        'fats',
        'carbohydrates',
        'fiber',
        'sauce_name',
        'sauce_name_ru',
        'sauce_name_ka',
        'sauce_weight_volume',
        'sauce_calories',
        'sauce_proteins',
        'sauce_fats',
        'sauce_carbohydrates',
        'sauce_fiber',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'discount_price' => 'decimal:2',
            'proteins' => 'decimal:2',
            'fats' => 'decimal:2',
            'carbohydrates' => 'decimal:2',
            'fiber' => 'decimal:2',
            'sauce_proteins' => 'decimal:2',
            'sauce_fats' => 'decimal:2',
            'sauce_carbohydrates' => 'decimal:2',
            'sauce_fiber' => 'decimal:2',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(DishCategory::class, 'dish_category_id');
    }

    /**
     * Получить название в зависимости от текущей локали.
     */
    public function getNameAttribute(?string $value): string
    {
        $locale = app()->getLocale();

        if ($locale === 'ka' && $this->name_ka) {
            return $this->name_ka;
        }

        if ($locale === 'ru' && $this->name_ru) {
            return $this->name_ru;
        }

        // Fallback на русский, затем на старое поле
        return $this->name_ru ?? $this->attributes['name'] ?? '';
    }

    /**
     * Получить описание в зависимости от текущей локали.
     */
    public function getDescriptionAttribute(?string $value): ?string
    {
        $locale = app()->getLocale();

        if ($locale === 'ka' && $this->description_ka) {
            return $this->description_ka;
        }

        if ($locale === 'ru' && $this->description_ru) {
            return $this->description_ru;
        }

        // Fallback на русский, затем на старое поле
        return $this->description_ru ?? $this->attributes['description'] ?? null;
    }

    /**
     * Получить название соуса в зависимости от текущей локали.
     */
    public function getSauceNameAttribute(?string $value): ?string
    {
        $locale = app()->getLocale();

        if ($locale === 'ka' && $this->sauce_name_ka) {
            return $this->sauce_name_ka;
        }

        if ($locale === 'ru' && $this->sauce_name_ru) {
            return $this->sauce_name_ru;
        }

        // Fallback на русский, затем на старое поле
        return $this->sauce_name_ru ?? $this->attributes['sauce_name'] ?? null;
    }
}
