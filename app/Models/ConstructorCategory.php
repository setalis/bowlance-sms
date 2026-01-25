<?php

namespace App\Models;

use Database\Factories\ConstructorCategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'sort_order',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(ConstructorProduct::class)->orderBy('sort_order');
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
