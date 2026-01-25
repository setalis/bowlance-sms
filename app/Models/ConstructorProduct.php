<?php

namespace App\Models;

use Database\Factories\ConstructorProductFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConstructorProduct extends Model
{
    use HasFactory;

    protected static function newFactory(): ConstructorProductFactory
    {
        return ConstructorProductFactory::new();
    }

    protected $fillable = [
        'constructor_category_id',
        'name',
        'name_ru',
        'name_ka',
        'price',
        'image',
        'sort_order',
        'description',
        'description_ru',
        'description_ka',
        'weight_volume',
        'calories',
        'proteins',
        'fats',
        'carbohydrates',
        'fiber',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'proteins' => 'decimal:2',
            'fats' => 'decimal:2',
            'carbohydrates' => 'decimal:2',
            'fiber' => 'decimal:2',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ConstructorCategory::class, 'constructor_category_id');
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
