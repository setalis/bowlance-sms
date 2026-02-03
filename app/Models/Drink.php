<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Drink extends Model
{
    /** @use HasFactory<\Database\Factories\DrinkFactory> */
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
        'image',
        'volume',
        'calories',
        'proteins',
        'fats',
        'carbohydrates',
        'fiber',
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
        ];
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
}
