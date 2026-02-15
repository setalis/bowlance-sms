<?php

namespace App\Providers;

use App\Models\Discount;
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

        View::share('pickupDiscount', Schema::hasTable('discounts') ? Discount::forPickup()->first() : null);
    }
}
