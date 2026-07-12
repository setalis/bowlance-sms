<?php

namespace App\Enums;

enum ConstructorType: string
{
    case Bowl = 'bowl';
    case Breakfast = 'breakfast';

    public function label(): string
    {
        return match ($this) {
            self::Bowl => 'Конструктор боулов',
            self::Breakfast => 'Конструктор завтраков',
        };
    }
}
