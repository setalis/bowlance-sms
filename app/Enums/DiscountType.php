<?php

namespace App\Enums;

enum DiscountType: string
{
    case Percent = 'percent';
    case Amount = 'amount';

    public function label(): string
    {
        return match ($this) {
            self::Percent => 'Процент',
            self::Amount => 'Сумма',
        };
    }
}
