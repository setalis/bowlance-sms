<?php

namespace App\Enums;

enum OrderStatus: string
{
    case New = 'new';
    case Unconfirmed = 'unconfirmed';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::New => 'Новый',
            self::Unconfirmed => 'Не подтверждён',
            self::InProgress => 'В работе',
            self::Completed => 'Выполнен',
            self::Cancelled => 'Отменён',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::New => 'info',
            self::Unconfirmed => 'warning',
            self::InProgress => 'primary',
            self::Completed => 'success',
            self::Cancelled => 'error',
        };
    }
}
