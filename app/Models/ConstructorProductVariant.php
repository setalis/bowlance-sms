<?php

namespace App\Models;

use App\Enums\ConstructorType;
use Database\Factories\ConstructorProductVariantFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConstructorProductVariant extends Model
{
    /** @use HasFactory<ConstructorProductVariantFactory> */
    use HasFactory;

    protected $fillable = [
        'constructor_product_id',
        'type',
        'price',
        'weight_volume',
        'calories',
        'proteins',
        'fats',
        'carbohydrates',
        'fiber',
        'poster_modification_id',
    ];

    protected function casts(): array
    {
        return [
            'type' => ConstructorType::class,
            'price' => 'decimal:2',
            'proteins' => 'decimal:2',
            'fats' => 'decimal:2',
            'carbohydrates' => 'decimal:2',
            'fiber' => 'decimal:2',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(ConstructorProduct::class, 'constructor_product_id');
    }
}
