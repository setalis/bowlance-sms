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

it('renders three column cart footer with accordion in cart drawer', function () {
    Discount::factory()->create([
        'size' => 15,
        'type' => DiscountType::Percent,
        'scope' => DiscountScope::Pickup,
    ]);

    $response = $this->get('/');

    $response->assertSuccessful();
    $response->assertSee(__('frontend.order_details'), false);
    $response->assertSee(__('frontend.order_details_hide'), false);
    $response->assertSee(__('frontend.total_to_pay'), false);
    $response->assertSee(__('frontend.delivery_fee_line'), false);
    $response->assertSee(__('frontend.discount_line'), false);
    $response->assertSee(__('frontend.cart_subtotal'), false);
    $response->assertSee(__('frontend.promotions_section'), false);
    $response->assertSee('deliveryProviderWolt', false);
    $response->assertSee('footerDeliveryFee', false);
    $response->assertSee('footerDiscountAmount', false);
    $response->assertDontSee(__('frontend.discount_pickup_hint', ['discount' => '−15%', 'total' => '85.00']), false);
});

it('renders delivery and pickup method summaries in checkout', function () {
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
    $response->assertSee(__('frontend.delivery_method_title'), false);
    $response->assertSee(__('frontend.pickup_method_title'), false);
    $response->assertSee(__('frontend.dine_in_method_title'), false);
    $response->assertSee('deliveryMethodSummary.figures', false);
    $response->assertSee('deliveryMethodSummary.caption', false);
    $response->assertSee('pickupMethodSummary.figures', false);
    $response->assertSee('pickupMethodSummary.caption', false);
    $response->assertSee('dineInMethodSummary.figures', false);
    $response->assertSee('dineInMethodSummary.caption', false);
    $response->assertSee('methodFigureClass(figure.tone)', false);
    $response->assertSee('grid grid-cols-1 sm:grid-cols-3 gap-2', false);
    $response->assertSee('sm:flex-col sm:items-center sm:text-center', false);
});
