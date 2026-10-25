<?php

namespace App\Services;

use App\Enums\DiscountType;
use App\Enums\OrderMode;
use App\Models\Discount;
use Illuminate\Support\Facades\Schema;

class ProductPriceService
{
    protected bool $pickupResolved = false;

    protected ?Discount $pickupDiscount = null;

    public function catalogAmount(float|string|null $price, float|string|null $discountPrice = null): float
    {
        if ($discountPrice !== null && $discountPrice !== '') {
            return round((float) $discountPrice, 2);
        }

        return round((float) $price, 2);
    }

    public function forAmount(float|string|null $deliveryPrice, ?OrderMode $mode = null): float
    {
        return $this->forMode((float) $deliveryPrice, $mode ?? OrderMode::current() ?? OrderMode::Delivery);
    }

    public function forMode(float $deliveryPrice, OrderMode $mode): float
    {
        $deliveryPrice = round($deliveryPrice, 2);

        if (! $this->includesPickupPercent($mode)) {
            return $deliveryPrice;
        }

        $size = (float) $this->pickupDiscount()->size;

        return round($deliveryPrice * (1 - ($size / 100)), 2);
    }

    public function includesPickupPercent(?OrderMode $mode = null): bool
    {
        $mode ??= OrderMode::current();

        if ($mode !== OrderMode::Onsite) {
            return false;
        }

        $discount = $this->pickupDiscount();

        return $discount !== null
            && $discount->type === DiscountType::Percent
            && (float) $discount->size > 0;
    }

    public function pickupPercent(): ?float
    {
        $discount = $this->pickupDiscount();

        if ($discount === null || $discount->type !== DiscountType::Percent) {
            return null;
        }

        return (float) $discount->size;
    }

    public function pickupDiscount(): ?Discount
    {
        if ($this->pickupResolved) {
            return $this->pickupDiscount;
        }

        $this->pickupResolved = true;

        try {
            if (! Schema::hasTable('discounts')) {
                return null;
            }

            $this->pickupDiscount = Discount::query()->forPickup()->first();
        } catch (\Throwable) {
            $this->pickupDiscount = null;
        }

        return $this->pickupDiscount;
    }
}
