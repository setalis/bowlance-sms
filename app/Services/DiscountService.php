<?php

namespace App\Services;

use App\Enums\DeliveryType;
use App\Models\Discount;

class DiscountService
{
    /**
     * @return array{discount: ?Discount, discount_amount: float, total: float}
     */
    public function calculateTotal(float $subtotal, DeliveryType $deliveryType, float $deliveryFee = 0): array
    {
        $discount = Discount::resolveForOrder($deliveryType, $subtotal);
        $discountAmount = $discount
            ? $discount->calculateDiscountAmount($subtotal)
            : 0.0;

        $total = max(0, round($subtotal - $discountAmount + $deliveryFee, 2));

        return [
            'discount' => $discount,
            'discount_amount' => $discountAmount,
            'total' => $total,
        ];
    }
}
