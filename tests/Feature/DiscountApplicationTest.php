<?php

use App\Enums\DeliveryType;
use App\Enums\DiscountScope;
use App\Enums\DiscountType;
use App\Models\Discount;
use App\Models\PhoneVerification;
use App\Services\DiscountService;

it('applies pickup percent discount through discount service', function () {
    Discount::factory()->create([
        'size' => 15,
        'type' => DiscountType::Percent,
        'scope' => DiscountScope::Pickup,
    ]);

    $result = app(DiscountService::class)->calculateTotal(100, DeliveryType::Pickup);

    expect($result['discount'])->not->toBeNull();
    expect($result['discount_amount'])->toBe(15.0);
    expect($result['total'])->toBe(85.0);
});

it('applies pickup percent discount for dine-in orders', function () {
    Discount::factory()->create([
        'size' => 15,
        'type' => DiscountType::Percent,
        'scope' => DiscountScope::Pickup,
    ]);

    $result = app(DiscountService::class)->calculateTotal(100, DeliveryType::DineIn);

    expect($result['discount'])->not->toBeNull();
    expect($result['discount_amount'])->toBe(15.0);
    expect($result['delivery_fee'])->toBe(0.0);
    expect($result['total'])->toBe(85.0);
});

it('does not apply cart total discount below threshold', function () {
    Discount::factory()->cartTotal(100)->create([
        'size' => 10,
        'type' => DiscountType::Percent,
    ]);

    $result = app(DiscountService::class)->calculateTotal(99, DeliveryType::Delivery);

    expect($result['discount'])->toBeNull();
    expect($result['discount_amount'])->toBe(0.0);
    expect($result['total'])->toBe(99.0);
});

it('applies cart total percent discount at threshold', function () {
    Discount::factory()->cartTotal(100)->create([
        'size' => 10,
        'type' => DiscountType::Percent,
    ]);

    $result = app(DiscountService::class)->calculateTotal(100, DeliveryType::Delivery);

    expect($result['discount'])->not->toBeNull();
    expect($result['discount_amount'])->toBe(10.0);
    expect($result['total'])->toBe(90.0);
});

it('applies highest matching cart total discount tier', function () {
    Discount::factory()->cartTotal(50)->create([
        'size' => 5,
        'type' => DiscountType::Percent,
    ]);

    Discount::factory()->cartTotal(100)->create([
        'size' => 10,
        'type' => DiscountType::Percent,
    ]);

    $result = app(DiscountService::class)->calculateTotal(120, DeliveryType::Delivery);

    expect((float) $result['discount']?->min_cart_total)->toBe(100.0);
    expect($result['discount_amount'])->toBe(12.0);
    expect($result['total'])->toBe(108.0);
});

it('does not apply pickup discount for delivery orders', function () {
    Discount::factory()->create([
        'size' => 15,
        'type' => DiscountType::Percent,
        'scope' => DiscountScope::Pickup,
    ]);

    $result = app(DiscountService::class)->calculateTotal(100, DeliveryType::Delivery);

    expect($result['discount'])->toBeNull();
    expect($result['total'])->toBe(100.0);
});

it('applies pickup discount when creating dine-in order', function () {
    Discount::factory()->create([
        'size' => 15,
        'type' => DiscountType::Percent,
        'scope' => DiscountScope::Pickup,
    ]);

    $response = $this->postJson('/orders', [
        'customer_name' => 'Тестовый Клиент',
        'customer_phone' => '+995555123456',
        'delivery_type' => DeliveryType::DineIn->value,
        'items' => [
            [
                'type' => 'bowl',
                'id' => 1,
                'name' => 'Тестовый боул',
                'price' => 100,
                'quantity' => 1,
            ],
        ],
    ]);

    $response->assertCreated();
    $response->assertJsonPath('order.total', '85.00');
    $response->assertJsonPath('order.delivery_type', DeliveryType::DineIn->value);

    $order = \App\Models\Order::latest()->first();
    expect((float) $order->subtotal)->toBe(100.0);
    expect((float) $order->delivery_fee)->toBe(0.0);
    expect((float) $order->total)->toBe(85.0);
});

it('applies cart total discount when creating delivery order', function () {
    Discount::factory()->cartTotal(100)->create([
        'size' => 10,
        'type' => DiscountType::Percent,
    ]);

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
                'price' => 100,
                'quantity' => 1,
            ],
        ],
    ]);

    $response->assertStatus(201);
    $response->assertJsonPath('order.total', '90.00');

    $order = \App\Models\Order::latest()->first();
    expect((float) $order->subtotal)->toBe(100.0);
    expect((float) $order->total)->toBe(90.0);
});

it('does not apply cart total discount below threshold when creating delivery order', function () {
    Discount::factory()->cartTotal(100)->create([
        'size' => 10,
        'type' => DiscountType::Percent,
    ]);

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
                'price' => 99,
                'quantity' => 1,
            ],
        ],
    ]);

    $response->assertStatus(201);
    $response->assertJsonPath('order.total', '99.00');
});
