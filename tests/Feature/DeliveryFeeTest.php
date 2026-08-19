<?php

use App\Enums\DeliveryType;
use App\Models\PhoneVerification;
use App\Services\DeliveryFeeService;
use App\Services\DiscountService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('charges delivery fee below free threshold', function () {
    $fee = app(DeliveryFeeService::class)->calculate(DeliveryType::Delivery, 40);

    expect($fee)->toBe(5.0);
});

it('does not charge delivery fee from free threshold', function () {
    $fee = app(DeliveryFeeService::class)->calculate(DeliveryType::Delivery, 50);

    expect($fee)->toBe(0.0);
});

it('does not charge delivery fee for pickup', function () {
    $fee = app(DeliveryFeeService::class)->calculate(DeliveryType::Pickup, 20);

    expect($fee)->toBe(0.0);
});

it('does not charge delivery fee for dine-in', function () {
    $fee = app(DeliveryFeeService::class)->calculate(DeliveryType::DineIn, 20);

    expect($fee)->toBe(0.0);
});

it('includes delivery fee in order total calculation', function () {
    $result = app(DiscountService::class)->calculateTotal(40, DeliveryType::Delivery);

    expect($result['delivery_fee'])->toBe(5.0);
    expect($result['total'])->toBe(45.0);
});

it('applies free delivery in order total calculation', function () {
    $result = app(DiscountService::class)->calculateTotal(50, DeliveryType::Delivery);

    expect($result['delivery_fee'])->toBe(0.0);
    expect($result['total'])->toBe(50.0);
});

it('stores delivery fee when creating delivery order below threshold', function () {
    $verification = PhoneVerification::factory()->verified()->create([
        'phone' => '+995555123456',
    ]);

    $response = $this->postJson('/orders', [
        'customer_name' => 'Тестовый Клиент',
        'customer_phone' => '+995555123456',
        'delivery_type' => 'delivery',
        'delivery_address' => 'Batumi, ул. Тестовая, 123',
        'delivery_city' => 'Batumi',
        'delivery_street' => 'ул. Тестовая',
        'delivery_house' => '123',
        'verification_request_id' => $verification->request_id,
        'items' => [
            [
                'type' => 'bowl',
                'id' => 1,
                'name' => 'Тестовый боул',
                'price' => 40,
                'quantity' => 1,
            ],
        ],
    ]);

    $response->assertStatus(201);
    $response->assertJsonPath('order.total', '45.00');

    $order = \App\Models\Order::latest()->first();
    expect((float) $order->subtotal)->toBe(40.0);
    expect((float) $order->delivery_fee)->toBe(5.0);
    expect((float) $order->total)->toBe(45.0);
});

it('exposes delivery config on homepage', function () {
    $response = $this->get('/');

    $response->assertSuccessful();
    $response->assertSee('window.deliveryConfig', false);
    $response->assertSee('fee: 5', false);
    $response->assertSee('freeFrom: 50', false);
});
