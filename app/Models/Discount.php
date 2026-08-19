<?php

namespace App\Models;

use App\Enums\DeliveryType;
use App\Enums\DiscountScope;
use App\Enums\DiscountType;
use Illuminate\Database\Eloquent\Builder;
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
        'min_cart_total',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'size' => 'decimal:2',
            'type' => DiscountType::class,
            'scope' => DiscountScope::class,
            'min_cart_total' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function scopeForPickup(Builder $query): Builder
    {
        return $query
            ->where('scope', DiscountScope::Pickup)
            ->where('is_active', true);
    }

    public function scopeForCartTotal(Builder $query): Builder
    {
        return $query
            ->where('scope', DiscountScope::CartTotal)
            ->where('is_active', true)
            ->orderByDesc('min_cart_total');
    }

    public function appliesTo(DeliveryType $deliveryType, float $subtotal): bool
    {
        return match ($this->scope) {
            DiscountScope::Pickup => $deliveryType->skipsDelivery(),
            DiscountScope::CartTotal => $deliveryType === DeliveryType::Delivery
                && $this->min_cart_total !== null
                && $subtotal >= (float) $this->min_cart_total,
        };
    }

    public static function resolveForOrder(DeliveryType $deliveryType, float $subtotal): ?self
    {
        return match ($deliveryType) {
            DeliveryType::Pickup, DeliveryType::DineIn => self::forPickup()->first(),
            DeliveryType::Delivery => self::forCartTotal()
                ->get()
                ->first(fn (self $discount) => $discount->appliesTo($deliveryType, $subtotal)),
        };
    }

    public function calculateDiscountAmount(float $subtotal): float
    {
        return match ($this->type) {
            DiscountType::Percent => round($subtotal * ($this->size / 100), 2),
            DiscountType::Amount => min((float) $this->size, $subtotal),
        };
    }
}
