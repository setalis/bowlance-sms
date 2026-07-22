<?php

namespace App\Models;

use Database\Factories\DishAddonFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DishAddon extends Model
{
    /** @use HasFactory<DishAddonFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'name_ru',
        'name_ka',
        'price',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function dishes(): BelongsToMany
    {
        return $this->belongsToMany(Dish::class, 'dish_dish_addon')
            ->withPivot(['poster_modification_id', 'price', 'sort_order'])
            ->orderByPivot('sort_order');
    }

    public function getNameAttribute(?string $value): string
    {
        $locale = app()->getLocale();

        if ($locale === 'ka' && $this->name_ka) {
            return $this->name_ka;
        }

        if ($locale === 'ru' && $this->name_ru) {
            return $this->name_ru;
        }

        return $this->name_ru ?? $this->attributes['name'] ?? '';
    }

    public function effectivePrice(): float
    {
        if ($this->relationLoaded('pivot') && $this->pivot?->price !== null) {
            return (float) $this->pivot->price;
        }

        return (float) $this->price;
    }
}
