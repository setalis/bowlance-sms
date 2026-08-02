<?php

use App\Models\ConstructorProduct;
use App\Models\Order;
use App\Models\PhoneVerification;
use App\Models\User;
use App\Services\PhoneAuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    Mail::fake();
    Http::fake([
        'joinposter.com/*' => Http::response([
            'response' => ['incoming_order_id' => '99'],
        ]),
    ]);
    config([
        'poster.enabled' => true,
        'poster.token' => 'test-token',
        'poster.spot_id' => 1,
        'poster.breakfast_constructor_product_id' => 131,
    ]);
});

it('сохраняет и отправляет в Poster локальный телефон как +995…', function () {
    $egg = ConstructorProduct::factory()->create([
        'name_ru' => 'Яйцо',
        'poster_breakfast_modification_id' => 86,
    ]);

    $response = $this->postJson('/orders', [
        'customer_name' => 'Тестовый Клиент',
        'customer_phone' => '0507082864',
        'customer_email' => 'test@example.com',
        'delivery_type' => 'pickup',
        'payment_method' => 'cash',
        'items' => [
            [
                'type' => 'breakfast',
                'id' => 1,
                'name' => 'Собранный завтрак',
                'price' => 12.50,
                'quantity' => 1,
                'products' => [
                    ['id' => $egg->id, 'name' => 'Яйцо', 'price' => 12.50, 'quantity' => 1],
                ],
            ],
        ],
    ]);

    $response->assertCreated();

    $order = Order::query()->first();

    expect($order)->not->toBeNull();
    expect($order->customer_phone)->toBe('+995507082864');

    Http::assertSent(function ($request) {
        return str_contains($request->url(), 'joinposter.com')
            && $request->isForm()
            && ($request->data()['phone'] ?? null) === '+995507082864';
    });
});

it('находит существующего пользователя по старому локальному формату телефона', function () {
    $user = User::factory()->create([
        'phone' => '0507082864',
    ]);

    $found = app(PhoneAuthService::class)->findOrCreateUser(
        '+995 507 08 28 64',
        null,
        'Клиент'
    );

    expect($found->id)->toBe($user->id);
    expect($found->fresh()->phone)->toBe('+995507082864');
});

it('сверяет верификацию телефона независимо от маски с пробелами', function () {
    $verification = PhoneVerification::factory()->verified()->create([
        'phone' => '+995555123456',
    ]);

    $egg = ConstructorProduct::factory()->create(['name_ru' => 'Яйцо']);

    $response = $this->postJson('/orders', [
        'customer_name' => 'Тестовый Клиент',
        'customer_phone' => '+995 555 12 34 56',
        'customer_email' => 'test@example.com',
        'delivery_type' => 'delivery',
        'delivery_city' => 'Batumi',
        'delivery_street' => 'ул. Тестовая',
        'delivery_house' => '123',
        'verification_request_id' => $verification->request_id,
        'items' => [
            [
                'type' => 'breakfast',
                'id' => 1,
                'name' => 'Собранный завтрак',
                'price' => 12.50,
                'quantity' => 1,
                'products' => [
                    ['id' => $egg->id, 'name' => 'Яйцо', 'price' => 12.50, 'quantity' => 1],
                ],
            ],
        ],
    ]);

    $response->assertCreated();

    expect(Order::query()->first()->customer_phone)->toBe('+995555123456');
});
