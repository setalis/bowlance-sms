<?php

use App\Models\PhoneVerification;

it('требует верификацию телефона при создании заказа', function () {
    $orderData = [
        'customer_name' => 'Тестовый Клиент',
        'customer_phone' => '+995555123456',
        'customer_email' => 'test@example.com',
        'delivery_address' => 'ул. Тестовая, 123',
        'items' => [
            [
                'type' => 'dish',
                'id' => 1,
                'name' => 'Тестовое блюдо',
                'price' => 15.50,
                'quantity' => 2,
            ],
        ],
    ];

    $response = $this->postJson('/orders', $orderData);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors('verification_request_id');
});

it('не позволяет создать заказ без верифицированного телефона', function () {
    $verification = PhoneVerification::factory()->create([
        'phone' => '+995555123456',
        'verified' => false,
    ]);

    $orderData = [
        'customer_name' => 'Тестовый Клиент',
        'customer_phone' => '+995555123456',
        'customer_email' => 'test@example.com',
        'verification_request_id' => $verification->request_id,
        'items' => [
            [
                'type' => 'dish',
                'id' => 1,
                'name' => 'Тестовое блюдо',
                'price' => 15.50,
                'quantity' => 2,
            ],
        ],
    ];

    $response = $this->postJson('/orders', $orderData);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors('customer_phone');
});

it('позволяет создать заказ с верифицированным телефоном', function () {
    $verification = PhoneVerification::factory()->verified()->create([
        'phone' => '+995555123456',
    ]);

    $orderData = [
        'customer_name' => 'Тестовый Клиент',
        'customer_phone' => '+995555123456',
        'customer_email' => 'test@example.com',
        'delivery_address' => 'ул. Тестовая, 123',
        'comment' => 'Тестовый комментарий',
        'verification_request_id' => $verification->request_id,
        'items' => [
            [
                'type' => 'bowl',
                'id' => 1,
                'name' => 'Тестовый боул',
                'price' => 15.50,
                'quantity' => 2,
                'calories' => 500,
                'products' => ['ingredient1', 'ingredient2'],
            ],
        ],
    ];

    $response = $this->postJson('/orders', $orderData);

    $response->assertStatus(201);
    $response->assertJsonStructure([
        'success',
        'message',
        'order' => [
            'id',
            'order_number',
            'total',
            'status',
        ],
    ]);

    $this->assertDatabaseHas('orders', [
        'customer_name' => 'Тестовый Клиент',
        'customer_phone' => '+995555123456',
        'phone_verified' => true,
    ]);
});

it('не позволяет создать заказ с истекшей верификацией', function () {
    $verification = PhoneVerification::factory()->verified()->expired()->create([
        'phone' => '+995555123456',
    ]);

    $orderData = [
        'customer_name' => 'Тестовый Клиент',
        'customer_phone' => '+995555123456',
        'verification_request_id' => $verification->request_id,
        'items' => [
            [
                'type' => 'bowl',
                'id' => 1,
                'name' => 'Тестовый боул',
                'price' => 15.50,
                'quantity' => 1,
            ],
        ],
    ];

    $response = $this->postJson('/orders', $orderData);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors('customer_phone');
});

it('не позволяет использовать чужую верификацию', function () {
    $verification = PhoneVerification::factory()->verified()->create([
        'phone' => '+995555999999',
    ]);

    $orderData = [
        'customer_name' => 'Тестовый Клиент',
        'customer_phone' => '+995555123456',
        'verification_request_id' => $verification->request_id,
        'items' => [
            [
                'type' => 'bowl',
                'id' => 1,
                'name' => 'Тестовый боул',
                'price' => 15.50,
                'quantity' => 1,
            ],
        ],
    ];

    $response = $this->postJson('/orders', $orderData);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors('customer_phone');
});

it('сохраняет данные о верификации телефона в заказе', function () {
    $verification = PhoneVerification::factory()->verified()->create([
        'phone' => '+995555123456',
    ]);

    $orderData = [
        'customer_name' => 'Тестовый Клиент',
        'customer_phone' => '+995555123456',
        'verification_request_id' => $verification->request_id,
        'items' => [
            [
                'type' => 'bowl',
                'id' => 1,
                'name' => 'Тестовый боул',
                'price' => 15.50,
                'quantity' => 1,
                'products' => ['ingredient1'],
            ],
        ],
    ];

    $response = $this->postJson('/orders', $orderData);

    $response->assertStatus(201);

    $this->assertDatabaseHas('orders', [
        'customer_phone' => '+995555123456',
        'phone_verified' => true,
    ]);

    $order = \App\Models\Order::latest()->first();
    expect($order->phone_verified_at)->not->toBeNull();
});
