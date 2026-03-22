<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
});

function baseOrderData(array $overrides = []): array
{
    return array_merge([
        'customer_name' => 'Тестовый Клиент',
        'customer_phone' => '+995555123456',
        'delivery_type' => 'delivery',
        'delivery_city' => 'Batumi',
        'delivery_street' => 'ул. Тестовая',
        'delivery_house' => '123',
        'verification_method' => 'callback',
        'items' => [
            [
                'type' => 'bowl',
                'id' => 1,
                'name' => 'Тестовый боул',
                'price' => 15.50,
                'quantity' => 1,
            ],
        ],
    ], $overrides);
}

it('сохраняет заказ без времени доставки (как можно быстрее)', function () {
    $response = $this->postJson('/orders', baseOrderData());

    $response->assertStatus(201);
    $this->assertDatabaseHas('orders', [
        'customer_phone' => '+995555123456',
        'delivery_time' => null,
    ]);
});

it('сохраняет заказ с корректным временем доставки', function () {
    $response = $this->postJson('/orders', baseOrderData(['delivery_time' => '14:30']));

    $response->assertStatus(201);
    $this->assertDatabaseHas('orders', [
        'customer_phone' => '+995555123456',
        'delivery_time' => '14:30',
    ]);
});

it('сохраняет заказ с граничным временем 10:00', function () {
    $response = $this->postJson('/orders', baseOrderData(['delivery_time' => '10:00']));

    $response->assertStatus(201);
    $this->assertDatabaseHas('orders', [
        'delivery_time' => '10:00',
    ]);
});

it('сохраняет заказ с граничным временем 20:00', function () {
    $response = $this->postJson('/orders', baseOrderData(['delivery_time' => '20:00']));

    $response->assertStatus(201);
    $this->assertDatabaseHas('orders', [
        'delivery_time' => '20:00',
    ]);
});

it('отклоняет время доставки вне диапазона 10:00-20:00', function () {
    $response = $this->postJson('/orders', baseOrderData(['delivery_time' => '09:30']));

    $response->assertStatus(422);
    $response->assertJsonValidationErrors('delivery_time');
});

it('отклоняет время доставки после 20:00', function () {
    $response = $this->postJson('/orders', baseOrderData(['delivery_time' => '20:30']));

    $response->assertStatus(422);
    $response->assertJsonValidationErrors('delivery_time');
});

it('отклоняет время в неверном формате', function () {
    $response = $this->postJson('/orders', baseOrderData(['delivery_time' => '2:30pm']));

    $response->assertStatus(422);
    $response->assertJsonValidationErrors('delivery_time');
});

it('принимает все допустимые слоты времени', function () {
    $slots = [];
    for ($minutes = 10 * 60; $minutes <= 20 * 60; $minutes += 30) {
        $slots[] = sprintf('%02d:%02d', intdiv($minutes, 60), $minutes % 60);
    }

    foreach ($slots as $slot) {
        $response = $this->postJson('/orders', baseOrderData(['delivery_time' => $slot]));
        $response->assertStatus(201, "Слот {$slot} должен быть разрешён");
    }
});
