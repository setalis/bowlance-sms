<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class DishCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_ru',
        'name_ka',
        'slug',
        'description',
        'description_ru',
        'description_ka',
        'image',
        'is_active',
        'sort',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'meta_image',
        'meta_url',
        'meta_type',
    ];

    /**
     * Генерирует уникальный слаг из названия категории.
     */
    public function generateSlug(string $name): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $count = 1;

        while (static::where('slug', $slug)->where('id', '!=', $this->id ?? 0)->exists()) {
            $slug = $originalSlug.'-'.$count;
            $count++;
        }

        return $slug;
    }

    /**
     * Автоматически генерирует слаг перед сохранением, если он не указан.
     */
    protected static function booted(): void
    {
        static::creating(function (DishCategory $category) {
            if (empty($category->slug)) {
                $nameForSlug = $category->name_ru ?? $category->name ?? '';
                if (! empty($nameForSlug)) {
                    $category->slug = $category->generateSlug($nameForSlug);
                }
            }
        });

        static::updating(function (DishCategory $category) {
            if (empty($category->slug)) {
                $nameForSlug = $category->name_ru ?? $category->name ?? '';
                if (! empty($nameForSlug)) {
                    $category->slug = $category->generateSlug($nameForSlug);
                }
            }
        });
    }

    public function dishes(): HasMany
    {
        return $this->hasMany(Dish::class)->orderBy('sort_order');
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
