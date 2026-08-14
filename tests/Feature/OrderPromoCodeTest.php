<?php

use App\Enums\DeliveryType;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Mail\NewOrderMail;
use App\Models\Dish;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PhoneVerification;
use App\Models\User;
use App\Services\PosterService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
});

it('сохраняет промокод при создании заказа', function () {
    $verification = PhoneVerification::factory()->verified()->create([
        'phone' => '+995555123456',
    ]);

    $response = $this->postJson('/orders', [
        'customer_name' => 'Тестовый Клиент',
        'customer_phone' => '+995555123456',
        'customer_email' => 'test@example.com',
        'delivery_type' => 'delivery',
        'delivery_address' => 'Batumi, ул. Тестовая, 123',
        'delivery_city' => 'Batumi',
        'delivery_street' => 'ул. Тестовая',
        'delivery_house' => '123',
        'promo_code' => 'FRIEND2026',
        'verification_request_id' => $verification->request_id,
        'items' => [
            [
                'type' => 'bowl',
                'id' => 1,
                'name' => 'Тестовый боул',
                'price' => 15.50,
                'quantity' => 2,
                'calories' => 500,
                'products' => ['ingredient1'],
            ],
        ],
    ]);

    $response->assertSuccessful();

    $this->assertDatabaseHas('orders', [
        'customer_phone' => '+995555123456',
        'promo_code' => 'FRIEND2026',
    ]);
});

it('письмо администратору содержит промокод', function () {
    Mail::fake();

    $verification = PhoneVerification::factory()->verified()->create([
        'phone' => '+995555100300',
    ]);

    $this->postJson('/orders', [
        'customer_name' => 'Тестовый Клиент',
        'customer_phone' => '+995555100300',
        'delivery_type' => 'delivery',
        'delivery_city' => 'Batumi',
        'delivery_street' => 'ул. Руставели',
        'delivery_house' => '1',
        'promo_code' => 'VIP-CODE',
        'verification_request_id' => $verification->request_id,
        'items' => [
            [
                'type' => 'bowl',
                'id' => 1,
                'name' => 'Тестовый боул',
                'price' => 12.00,
                'quantity' => 1,
                'products' => ['Ingredient A'],
            ],
        ],
    ])->assertSuccessful();

    Mail::assertSent(NewOrderMail::class, function (NewOrderMail $mail) {
        $html = $mail->render();

        return str_contains($html, 'Промокод')
            && str_contains($html, 'VIP-CODE');
    });
});

it('передаёт промокод в комментарий Poster', function () {
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

    $dish = Dish::factory()->create(['poster_product_id' => 169]);
    $user = User::factory()->create();

    $order = Order::create([
        'user_id' => $user->id,
        'order_number' => 'ORD-PROMO-001',
        'customer_name' => 'Тест Клиент',
        'customer_phone' => '+995555123456',
        'delivery_type' => DeliveryType::Delivery,
        'delivery_address' => 'ул. Тестовая, 1',
        'promo_code' => 'FRIEND2026',
        'comment' => 'Без лука',
        'subtotal' => 250.00,
        'delivery_fee' => 0,
        'total' => 250.00,
        'status' => OrderStatus::New,
        'payment_method' => PaymentMethod::Cash,
        'phone_verified' => true,
        'needs_callback' => false,
        'leave_at_door' => false,
    ]);

    OrderItem::create([
        'order_id' => $order->id,
        'item_type' => 'dish',
        'dish_id' => $dish->id,
        'name' => 'Тестовое блюдо',
        'price' => 250.00,
        'quantity' => 1,
        'subtotal' => 250.00,
    ]);

    $result = app(PosterService::class)->createIncomingOrder($order->load('items.dish'));

    expect($result)->not->toBeNull();

    Http::assertSent(function ($request) {
        $comment = $request->data()['comment'] ?? '';

        return str_contains($comment, 'Сумма товаров: 250.00 ₾')
            && str_contains($comment, 'Доставка: Бесплатно')
            && str_contains($comment, 'Итого к оплате: 250.00 ₾')
            && str_contains($comment, 'Способ: доставка')
            && str_contains($comment, "---\nПромокод: FRIEND2026")
            && str_contains($comment, 'Комментарий клиента: Без лука');
    });
});

it('передаёт только промокод в Poster если комментария нет', function () {
    config([
        'poster.enabled' => true,
        'poster.token' => 'test-token',
        'poster.spot_id' => 1,
    ]);

    Http::fake([
        'joinposter.com/*' => Http::response([
            'response' => ['incoming_order_id' => '100'],
        ]),
    ]);

    $dish = Dish::factory()->create(['poster_product_id' => 170]);
    $user = User::factory()->create();

    $order = Order::create([
        'user_id' => $user->id,
        'order_number' => 'ORD-PROMO-002',
        'customer_name' => 'Тест Клиент',
        'customer_phone' => '+995555123457',
        'delivery_type' => DeliveryType::Pickup,
        'promo_code' => 'ONLY-PROMO',
        'subtotal' => 100.00,
        'delivery_fee' => 0,
        'total' => 100.00,
        'status' => OrderStatus::New,
        'payment_method' => PaymentMethod::Cash,
        'phone_verified' => true,
        'needs_callback' => false,
        'leave_at_door' => false,
    ]);

    OrderItem::create([
        'order_id' => $order->id,
        'item_type' => 'dish',
        'dish_id' => $dish->id,
        'name' => 'Тестовое блюдо',
        'price' => 100.00,
        'quantity' => 1,
        'subtotal' => 100.00,
    ]);

    app(PosterService::class)->createIncomingOrder($order->load('items.dish'));

    Http::assertSent(function ($request) {
        $comment = $request->data()['comment'] ?? '';

        return str_contains($comment, 'Сумма товаров: 100.00 ₾')
            && str_contains($comment, 'Доставка: —')
            && str_contains($comment, 'Итого к оплате: 100.00 ₾')
            && str_contains($comment, 'Способ: самовывоз')
            && str_contains($comment, "---\nПромокод: ONLY-PROMO")
            && ! str_contains($comment, 'Комментарий клиента:');
    });
});
