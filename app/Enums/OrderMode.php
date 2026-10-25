<?php

namespace App\Enums;

enum OrderMode: string
{
    case Onsite = 'onsite';
    case Delivery = 'delivery';

    public const SESSION_KEY = 'order_mode';

    public const WINDOW_COOKIE = 'order_mode_window';

    public static function current(): ?self
    {
        $value = session(self::SESSION_KEY);
        $mode = is_string($value) ? self::tryFrom($value) : null;

        if ($mode === null) {
            return null;
        }

        if (request()->cookie(self::WINDOW_COOKIE) !== '1') {
            session()->forget(self::SESSION_KEY);

            return null;
        }

        return $mode;
    }

    public static function put(self $mode): void
    {
        session([self::SESSION_KEY => $mode->value]);

        request()->cookies->set(self::WINDOW_COOKIE, '1');

        cookie()->queue(cookie(self::WINDOW_COOKIE, '1', 0));
    }

    public function allows(DeliveryType $deliveryType): bool
    {
        return match ($this) {
            self::Onsite => $deliveryType === DeliveryType::Pickup || $deliveryType === DeliveryType::DineIn,
            self::Delivery => $deliveryType === DeliveryType::Delivery,
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Onsite => __('frontend.order_mode_onsite'),
            self::Delivery => __('frontend.order_mode_delivery'),
        };
    }

    public function shortLabel(): string
    {
        return match ($this) {
            self::Onsite => __('frontend.order_mode_onsite_short'),
            self::Delivery => __('frontend.order_mode_delivery_short'),
        };
    }
}
