<?php

namespace App\Models;

use App\Enums\ConstructorType;
use Database\Factories\ConstructorCategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ConstructorCategory extends Model
{
    use HasFactory;

    protected static function newFactory(): ConstructorCategoryFactory
    {
        return ConstructorCategoryFactory::new();
    }

    protected $fillable = [
        'name',
        'name_ru',
        'name_ka',
        'icon_class',
        'sort_order',
        'type',
    ];

    protected function casts(): array
    {
        return [
            'type' => ConstructorType::class,
        ];
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(ConstructorProduct::class, 'constructor_category_product')
            ->orderBy('constructor_products.sort_order');
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
}
