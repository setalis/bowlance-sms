<?php

use App\Enums\DeliveryType;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    Mail::fake();
    Http::fake();
});

it('сохраняет комментарий для заказа на вынос и в заведении', function (DeliveryType $deliveryType) {
    $response = $this->postJson('/orders', [
        'customer_name' => 'Тестовый Клиент',
        'customer_phone' => '+995555123456',
        'delivery_type' => $deliveryType->value,
        'comment' => 'Без лука',
        'items' => [
            [
                'type' => 'bowl',
                'id' => 1,
                'name' => 'Тестовый боул',
                'price' => 15.50,
                'quantity' => 1,
            ],
        ],
    ]);

    $response->assertCreated();

    $order = Order::latest('id')->first();

    expect($order)->not->toBeNull()
        ->and($order->comment)->toBe('Без лука')
        ->and($order->delivery_type)->toBe($deliveryType);
})->with([
    'pickup' => DeliveryType::Pickup,
    'dine_in' => DeliveryType::DineIn,
]);
