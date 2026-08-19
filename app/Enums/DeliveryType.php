<?php

namespace App\Enums;

enum DeliveryType: string
{
    case Delivery = 'delivery';
    case Pickup = 'pickup';
    case DineIn = 'dine_in';

    public function label(): string
    {
        return match ($this) {
            self::Delivery => 'Доставка',
            self::Pickup => 'Самовывоз',
            self::DineIn => 'В заведении',
        };
    }

    public function skipsDelivery(): bool
    {
        return $this !== self::Delivery;
    }
}
