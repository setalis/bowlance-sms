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
        'slug',
        'description',
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
            if (empty($category->slug) && ! empty($category->name)) {
                $category->slug = $category->generateSlug($category->name);
            }
        });

        static::updating(function (DishCategory $category) {
            if (empty($category->slug) && ! empty($category->name)) {
                $category->slug = $category->generateSlug($category->name);
            }
        });
    }

    public function dishes(): HasMany
    {
        return $this->hasMany(Dish::class)->orderBy('sort_order');
    }
}
