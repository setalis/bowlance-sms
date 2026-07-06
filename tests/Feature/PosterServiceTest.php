<?php

use App\Enums\DeliveryType;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Models\Dish;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Services\PosterService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

function makePosterOrder(array $orderAttributes = []): Order
{
    $user = User::factory()->create();

    return Order::create(array_merge([
        'user_id' => $user->id,
        'order_number' => 'ORD-TEST-001',
        'customer_name' => 'Тест Клиент',
        'customer_phone' => '+995555123456',
        'customer_email' => 'test@example.com',
        'delivery_type' => DeliveryType::Delivery,
        'delivery_address' => 'ул. Тестовая, 1',
        'subtotal' => 500.00,
        'delivery_fee' => 0,
        'total' => 500.00,
        'status' => OrderStatus::New,
        'payment_method' => PaymentMethod::Cash,
        'phone_verified' => true,
        'needs_callback' => false,
        'leave_at_door' => false,
    ], $orderAttributes));
}

it('отправляет заказ в Poster когда блюдо имеет poster_product_id', function () {
    config([
        'poster.enabled' => true,
        'poster.token' => 'test-token',
        'poster.spot_id' => 1,
    ]);

    Http::fake([
        'joinposter.com/*' => Http::response([
            'response' => ['incoming_order_id' => '42'],
        ]),
    ]);

    $dish = Dish::factory()->create(['poster_product_id' => 169]);
    $order = makePosterOrder();

    OrderItem::create([
        'order_id' => $order->id,
        'item_type' => 'dish',
        'dish_id' => $dish->id,
        'name' => 'Тестовое блюдо',
        'price' => 250.00,
        'quantity' => 2,
        'subtotal' => 500.00,
    ]);

    $service = new PosterService;
    $result = $service->createIncomingOrder($order->load('items.dish'));

    Http::assertSent(function ($request) {
        $body = $request->data();

        return str_contains($request->url(), 'joinposter.com')
            && str_contains($request->url(), 'token=test-token')
            && $body['spot_id'] === 1
            && $body['phone'] === '+995555123456'
            && count($body['products']) === 1
            && $body['products'][0]['product_id'] === 169
            && $body['products'][0]['count'] === 2;
    });

    expect($result)->not->toBeNull();
    expect($order->fresh()->poster_order_id)->toBe('42');
});

it('не отправляет заказ в Poster когда у блюда нет poster_product_id', function () {
    config([
        'poster.enabled' => true,
        'poster.token' => 'test-token',
        'poster.spot_id' => 1,
    ]);

    Http::fake();

    $dish = Dish::factory()->create(['poster_product_id' => null]);
    $order = makePosterOrder();

    OrderItem::create([
        'order_id' => $order->id,
        'item_type' => 'dish',
        'dish_id' => $dish->id,
        'name' => 'Тестовое блюдо',
        'price' => 250.00,
        'quantity' => 1,
        'subtotal' => 250.00,
    ]);

    $service = new PosterService;
    $result = $service->createIncomingOrder($order->load('items.dish'));

    Http::assertNothingSent();
    expect($result)->toBeNull();
    expect($order->fresh()->poster_order_id)->toBeNull();
});

it('не отправляет заказ в Poster когда интеграция выключена', function () {
    config([
        'poster.enabled' => false,
        'poster.token' => 'test-token',
        'poster.spot_id' => 1,
    ]);

    Http::fake();

    $dish = Dish::factory()->create(['poster_product_id' => 169]);
    $order = makePosterOrder();

    OrderItem::create([
        'order_id' => $order->id,
        'item_type' => 'dish',
        'dish_id' => $dish->id,
        'name' => 'Тестовое блюдо',
        'price' => 250.00,
        'quantity' => 1,
        'subtotal' => 250.00,
    ]);

    $service = new PosterService;
    $result = $service->createIncomingOrder($order->load('items.dish'));

    Http::assertNothingSent();
    expect($result)->toBeNull();
});

it('не отправляет заказ в Poster когда токен не задан', function () {
    config([
        'poster.enabled' => true,
        'poster.token' => null,
        'poster.spot_id' => 1,
    ]);

    Http::fake();

    $dish = Dish::factory()->create(['poster_product_id' => 169]);
    $order = makePosterOrder();

    OrderItem::create([
        'order_id' => $order->id,
        'item_type' => 'dish',
        'dish_id' => $dish->id,
        'name' => 'Тестовое блюдо',
        'price' => 250.00,
        'quantity' => 1,
        'subtotal' => 250.00,
    ]);

    $service = new PosterService;
    $result = $service->createIncomingOrder($order->load('items.dish'));

    Http::assertNothingSent();
    expect($result)->toBeNull();
});

it('логирует ошибку когда Poster возвращает error с HTTP 200', function () {
    config([
        'poster.enabled' => true,
        'poster.token' => 'test-token',
        'poster.spot_id' => 1,
    ]);

    Http::fake([
        'joinposter.com/*' => Http::response([
            'error' => 32,
            'message' => 'Query parameters error',
        ]),
    ]);

    $dish = Dish::factory()->create(['poster_product_id' => 169]);
    $order = makePosterOrder();

    OrderItem::create([
        'order_id' => $order->id,
        'item_type' => 'dish',
        'dish_id' => $dish->id,
        'name' => 'Тестовое блюдо',
        'price' => 250.00,
        'quantity' => 1,
        'subtotal' => 250.00,
    ]);

    $service = new PosterService;
    $result = $service->createIncomingOrder($order->load('items.dish'));

    expect($result)->toBeNull();
    expect($order->fresh()->poster_order_id)->toBeNull();
});

it('пропускает bowl и drink элементы при отправке в Poster', function () {
    config([
        'poster.enabled' => true,
        'poster.token' => 'test-token',
        'poster.spot_id' => 1,
    ]);

    Http::fake([
        'joinposter.com/*' => Http::response([
            'response' => ['incoming_order_id' => '99'],
        ]),
    ]);

    $dish = Dish::factory()->create(['poster_product_id' => 200]);
    $order = makePosterOrder();

    OrderItem::create([
        'order_id' => $order->id,
        'item_type' => 'dish',
        'dish_id' => $dish->id,
        'name' => 'Тестовое блюдо',
        'price' => 250.00,
        'quantity' => 1,
        'subtotal' => 250.00,
    ]);

    OrderItem::create([
        'order_id' => $order->id,
        'item_type' => 'bowl',
        'dish_id' => null,
        'name' => 'Авторский боул',
        'price' => 300.00,
        'quantity' => 1,
        'subtotal' => 300.00,
        'bowl_products' => [],
    ]);

    OrderItem::create([
        'order_id' => $order->id,
        'item_type' => 'drink',
        'dish_id' => null,
        'name' => 'Вода',
        'price' => 50.00,
        'quantity' => 2,
        'subtotal' => 100.00,
    ]);

    $service = new PosterService;
    $service->createIncomingOrder($order->load('items.dish'));

    Http::assertSent(function ($request) {
        $body = $request->data();

        return count($body['products']) === 1
            && $body['products'][0]['product_id'] === 200;
    });
});
