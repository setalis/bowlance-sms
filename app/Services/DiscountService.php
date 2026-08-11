<?php

namespace App\Services;

use App\Enums\DeliveryType;
use App\Models\Discount;

class DiscountService
{
    public function __construct(protected DeliveryFeeService $deliveryFeeService) {}

    /**
     * @return array{discount: ?Discount, discount_amount: float, delivery_fee: float, total: float}
     */
    public function calculateTotal(float $subtotal, DeliveryType $deliveryType): array
    {
        $discount = Discount::resolveForOrder($deliveryType, $subtotal);
        $discountAmount = $discount
            ? $discount->calculateDiscountAmount($subtotal)
            : 0.0;

        $deliveryFee = $this->deliveryFeeService->calculate($deliveryType, $subtotal);
        $total = max(0, round($subtotal - $discountAmount + $deliveryFee, 2));

        return [
            'discount' => $discount,
            'discount_amount' => $discountAmount,
            'delivery_fee' => $deliveryFee,
            'total' => $total,
        ];
    }
}
