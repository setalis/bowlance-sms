<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'item_type',
        'dish_id',
        'name',
        'price',
        'quantity',
        'subtotal',
        'calories',
        'proteins',
        'fats',
        'carbohydrates',
        'bowl_products',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'proteins' => 'decimal:2',
            'fats' => 'decimal:2',
            'carbohydrates' => 'decimal:2',
            'bowl_products' => 'array',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function dish(): BelongsTo
    {
        return $this->belongsTo(Dish::class);
    }
}
