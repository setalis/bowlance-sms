<?php

namespace App\Models;

use App\Enums\ConstructorType;
use Database\Factories\ConstructorProductFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ConstructorProduct extends Model
{
    use HasFactory;

    protected static function newFactory(): ConstructorProductFactory
    {
        return ConstructorProductFactory::new();
    }

    protected $fillable = [
        'name',
        'name_ru',
        'name_ka',
        'image',
        'sort_order',
        'description',
        'description_ru',
        'description_ka',
    ];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(ConstructorCategory::class, 'constructor_category_product')
            ->orderBy('sort_order');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ConstructorProductVariant::class);
    }

    public function variantFor(ConstructorType|string $type): ?ConstructorProductVariant
    {
        $type = $type instanceof ConstructorType ? $type : ConstructorType::from($type);

        if ($this->relationLoaded('variants')) {
            return $this->variants->first(fn (ConstructorProductVariant $variant) => $variant->type === $type);
        }

        return $this->variants()->where('type', $type)->first();
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

    public function getDescriptionAttribute(?string $value): ?string
    {
        $locale = app()->getLocale();

        if ($locale === 'ka' && $this->description_ka) {
            return $this->description_ka;
        }

        if ($locale === 'ru' && $this->description_ru) {
            return $this->description_ru;
        }

        return $this->description_ru ?? $this->attributes['description'] ?? null;
    }
}
