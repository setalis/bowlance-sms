<?php

namespace App\Providers;

use App\Models\Discount;
use App\Services\WoltDriveService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Гарантируем fallback-локаль для переводов (важно на хостинге после config:cache)
        $this->app->setFallbackLocale(config('app.fallback_locale', 'ru'));

        View::share('pickupDiscount', $this->getPickupDiscount());
        View::share('woltDeliveryEnabled', $this->app->make(WoltDriveService::class)->isEnabled());
    }

    protected function getPickupDiscount(): ?Discount
    {
        try {
            if (! Schema::hasTable('discounts')) {
                return null;
            }

            return Discount::forPickup()->first();
        } catch (\Throwable) {
            return null;
        }
    }
}
