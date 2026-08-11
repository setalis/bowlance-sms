<?php

use App\Enums\DiscountScope;
use App\Enums\DiscountType;
use App\Models\Discount;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('exposes empty discount config when no discounts exist', function () {
    $response = $this->get('/');

    $response->assertSuccessful();
    $response->assertSee('window.discountConfig', false);
    $response->assertSee('pickup: null', false);
    $response->assertSee('cartTotal: []', false);
});

it('exposes active discounts in discount config on homepage', function () {
    Discount::factory()->create([
        'size' => 15,
        'type' => DiscountType::Percent,
        'scope' => DiscountScope::Pickup,
    ]);

    Discount::factory()->cartTotal(100)->create([
        'size' => 10,
        'type' => DiscountType::Percent,
    ]);

    $response = $this->get('/');

    $response->assertSuccessful();
    $response->assertSee('window.discountConfig', false);
    $response->assertSee('"size":15', false);
    $response->assertSee('"min_cart_total":100', false);
});

it('renders order summary component in cart drawer', function () {
    Discount::factory()->create([
        'size' => 15,
        'type' => DiscountType::Percent,
        'scope' => DiscountScope::Pickup,
    ]);

    $response = $this->get('/');

    $response->assertSuccessful();
    $response->assertSee(__('frontend.cart_subtotal'), false);
    $response->assertSee(__('frontend.total_pickup_preview'), false);
    $response->assertSee(__('frontend.total_delivery_preview'), false);
});
