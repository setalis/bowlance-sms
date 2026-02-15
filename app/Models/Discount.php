<?php

namespace App\Models;

use App\Enums\DiscountType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'size',
        'type',
        'scope',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'size' => 'decimal:2',
            'type' => DiscountType::class,
            'is_active' => 'boolean',
        ];
    }

    public function scopeForPickup($query)
    {
        return $query->where('scope', 'pickup')->where('is_active', true);
    }

    /**
     * Рассчитать сумму скидки от заданной суммы.
     */
    public function calculateDiscountAmount(float $subtotal): float
    {
        return match ($this->type) {
            DiscountType::Percent => round($subtotal * ($this->size / 100), 2),
            DiscountType::Amount => min((float) $this->size, $subtotal),
        };
    }
}
