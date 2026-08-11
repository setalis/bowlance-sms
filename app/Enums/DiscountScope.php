<?php

namespace App\Enums;

enum DiscountScope: string
{
    case Pickup = 'pickup';
    case CartTotal = 'cart_total';

    public function label(): string
    {
        return match ($this) {
            self::Pickup => 'Самовывоз',
            self::CartTotal => 'По сумме корзины (доставка)',
        };
    }
}
