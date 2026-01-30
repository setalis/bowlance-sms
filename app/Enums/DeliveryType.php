<?php

namespace App\Enums;

enum DeliveryType: string
{
    case Delivery = 'delivery';
    case Pickup = 'pickup';

    public function label(): string
    {
        return match ($this) {
            self::Delivery => 'Доставка',
            self::Pickup => 'Самовывоз',
        };
    }
}
