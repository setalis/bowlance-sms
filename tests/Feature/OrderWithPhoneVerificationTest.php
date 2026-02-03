<?php

use App\Models\PhoneVerification;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
});

it('требует верификацию телефона при создании заказа', function () {
    $orderData = [
        'customer_name' => 'Тестовый Клиент',
        'customer_phone' => '+995555123456',
        'customer_email' => 'test@example.com',
        'delivery_type' => 'delivery',
        'delivery_address' => 'ул. Тестовая, 123',
        'items' => [
            [
                'type' => 'bowl',
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
        'delivery_type' => 'delivery',
        'verification_request_id' => $verification->request_id,
        'items' => [
            [
                'type' => 'bowl',
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
        'delivery_type' => 'delivery',
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
        'delivery_type' => 'pickup',
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
        'delivery_type' => 'pickup',
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
        'delivery_type' => 'pickup',
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

it('требует указания типа доставки при создании заказа', function () {
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
            ],
        ],
    ];

    $response = $this->postJson('/orders', $orderData);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors('delivery_type');
});

it('требует адрес доставки при выборе доставки', function () {
    $verification = PhoneVerification::factory()->verified()->create([
        'phone' => '+995555123456',
    ]);

    $orderData = [
        'customer_name' => 'Тестовый Клиент',
        'customer_phone' => '+995555123456',
        'delivery_type' => 'delivery',
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
    $response->assertJsonValidationErrors('delivery_address');
});

it('не требует адрес доставки при выборе самовывоза', function () {
    $verification = PhoneVerification::factory()->verified()->create([
        'phone' => '+995555123456',
    ]);

    $orderData = [
        'customer_name' => 'Тестовый Клиент',
        'customer_phone' => '+995555123456',
        'delivery_type' => 'pickup',
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
        'delivery_type' => 'pickup',
    ]);
});

it('создает нового пользователя и авторизует его при оформлении заказа', function () {
    $verification = PhoneVerification::factory()->verified()->create([
        'phone' => '+995555123456',
    ]);

    $orderData = [
        'customer_name' => 'Новый Клиент',
        'customer_phone' => '+995555123456',
        'customer_email' => 'new@example.com',
        'delivery_type' => 'pickup',
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

    $response->assertStatus(201);

    // Проверяем что пользователь создан
    $this->assertDatabaseHas('users', [
        'phone' => '+995555123456',
        'name' => 'Новый Клиент',
        'email' => 'new@example.com',
        'role' => 'user',
    ]);

    // Проверяем что пользователь авторизован
    expect(auth()->check())->toBeTrue();
    expect(auth()->user()->phone)->toBe('+995555123456');

    // Проверяем что заказ привязан к созданному пользователю
    $order = \App\Models\Order::latest()->first();
    expect($order->user_id)->toBe(auth()->id());
});

it('авторизует существующего пользователя по телефону при оформлении заказа', function () {
    // Создаем существующего пользователя
    $existingUser = \App\Models\User::factory()->create([
        'phone' => '+995555123456',
        'name' => 'Существующий Клиент',
        'email' => 'existing@example.com',
    ]);

    $verification = PhoneVerification::factory()->verified()->create([
        'phone' => '+995555123456',
    ]);

    $orderData = [
        'customer_name' => 'Другое Имя',
        'customer_phone' => '+995555123456',
        'delivery_type' => 'pickup',
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

    $response->assertStatus(201);

    // Проверяем что новый пользователь НЕ создан
    expect(\App\Models\User::count())->toBe(1);

    // Проверяем что авторизован существующий пользователь
    expect(auth()->check())->toBeTrue();
    expect(auth()->id())->toBe($existingUser->id);

    // Проверяем что заказ привязан к существующему пользователю
    $order = \App\Models\Order::latest()->first();
    expect($order->user_id)->toBe($existingUser->id);
});

it('генерирует email-заглушку если email не указан при создании пользователя', function () {
    $verification = PhoneVerification::factory()->verified()->create([
        'phone' => '+995555123456',
    ]);

    $orderData = [
        'customer_name' => 'Клиент без email',
        'customer_phone' => '+995555123456',
        'delivery_type' => 'pickup',
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

    $response->assertStatus(201);

    // Проверяем что создан пользователь с email-заглушкой
    $this->assertDatabaseHas('users', [
        'phone' => '+995555123456',
        'email' => '995555123456@bowlance.ge',
    ]);
});

it('требует подтверждения при попытке переключения на другого пользователя', function () {
    // Создаем двух пользователей
    $currentUser = \App\Models\User::factory()->create([
        'phone' => '+995555111111',
    ]);

    $targetUser = \App\Models\User::factory()->create([
        'phone' => '+995555222222',
    ]);

    // Авторизуем первого пользователя
    $this->actingAs($currentUser);

    $verification = PhoneVerification::factory()->verified()->create([
        'phone' => '+995555222222',
    ]);

    $orderData = [
        'customer_name' => 'Тест',
        'customer_phone' => '+995555222222',
        'delivery_type' => 'pickup',
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

    $response->assertStatus(409);
    $response->assertJson([
        'success' => false,
        'requires_confirmation' => true,
    ]);
    $response->assertJsonStructure([
        'message',
        'target_user' => ['id', 'name', 'phone'],
    ]);

    // Заказ не должен быть создан
    expect(\App\Models\Order::count())->toBe(0);

    // Текущий пользователь должен остаться авторизованным
    expect(auth()->id())->toBe($currentUser->id);
});

it('переключает пользователя после подтверждения', function () {
    // Создаем двух пользователей
    $currentUser = \App\Models\User::factory()->create([
        'phone' => '+995555111111',
    ]);

    $targetUser = \App\Models\User::factory()->create([
        'phone' => '+995555222222',
        'name' => 'Целевой Пользователь',
    ]);

    // Авторизуем первого пользователя
    $this->actingAs($currentUser);

    $verification = PhoneVerification::factory()->verified()->create([
        'phone' => '+995555222222',
    ]);

    $orderData = [
        'customer_name' => 'Тест',
        'customer_phone' => '+995555222222',
        'delivery_type' => 'pickup',
        'verification_request_id' => $verification->request_id,
        'confirm_switch_user' => true,
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

    $response->assertStatus(201);

    // Проверяем что пользователь переключился
    expect(auth()->id())->toBe($targetUser->id);

    // Проверяем что заказ создан и привязан к целевому пользователю
    $order = \App\Models\Order::latest()->first();
    expect($order->user_id)->toBe($targetUser->id);
});

it('не требует подтверждения если телефон принадлежит текущему пользователю', function () {
    $user = \App\Models\User::factory()->create([
        'phone' => '+995555123456',
    ]);

    // Авторизуем пользователя
    $this->actingAs($user);

    $verification = PhoneVerification::factory()->verified()->create([
        'phone' => '+995555123456',
    ]);

    $orderData = [
        'customer_name' => 'Тест',
        'customer_phone' => '+995555123456',
        'delivery_type' => 'pickup',
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

    $response->assertStatus(201);

    // Проверяем что заказ создан
    $order = \App\Models\Order::latest()->first();
    expect($order->user_id)->toBe($user->id);

    // Проверяем что пользователь остался тем же
    expect(auth()->id())->toBe($user->id);
});
