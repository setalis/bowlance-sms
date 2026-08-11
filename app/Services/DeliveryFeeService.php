<?php

namespace App\Services;

use App\Enums\DeliveryType;

class DeliveryFeeService
{
    public function calculate(DeliveryType $deliveryType, float $subtotal): float
    {
        if ($deliveryType !== DeliveryType::Delivery) {
            return 0.0;
        }

        if ($subtotal >= (float) config('delivery.free_from', 50)) {
            return 0.0;
        }

        return (float) config('delivery.fee', 5);
    }
}
