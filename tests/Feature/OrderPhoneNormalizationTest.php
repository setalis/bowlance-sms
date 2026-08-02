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

function postBreakfastOrder(array $payload = []): Illuminate\Testing\TestResponse
{
    $egg = ConstructorProduct::factory()->create([
        'name_ru' => 'Яйцо',
        'poster_breakfast_modification_id' => 86,
    ]);

    return test()->postJson('/orders', array_merge([
        'customer_name' => 'Тестовый Клиент',
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
    ], $payload));
}

it('сохраняет и отправляет в Poster грузинский номер без кода страны', function () {
    postBreakfastOrder(['customer_phone' => '555 12 34 56'])->assertCreated();

    expect(Order::query()->first()->customer_phone)->toBe('+995555123456');

    Http::assertSent(function ($request) {
        return str_contains($request->url(), 'joinposter.com')
            && $request->isForm()
            && ($request->data()['phone'] ?? null) === '+995555123456';
    });
});

it('сохраняет и отправляет в Poster украинский номер в международном формате', function () {
    postBreakfastOrder(['customer_phone' => '+380507082864'])->assertCreated();

    expect(Order::query()->first()->customer_phone)->toBe('+380507082864');

    Http::assertSent(function ($request) {
        return ($request->data()['phone'] ?? null) === '+380507082864';
    });
});

it('отклоняет номер, который не существует ни в одной стране', function (string $phone) {
    postBreakfastOrder(['customer_phone' => $phone])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('customer_phone');

    expect(Order::query()->count())->toBe(0);
    Http::assertNothingSent();
})->with([
    'ukrainian local without country code' => '0507082864',
    'nonexistent georgian prefix' => '+995222222222',
    'too short' => '55050505',
]);

it('находит существующего пользователя по старому локальному формату телефона', function () {
    $user = User::factory()->create([
        'phone' => '555948217',
    ]);

    $found = app(PhoneAuthService::class)->findOrCreateUser(
        '+995 555 94 82 17',
        null,
        'Клиент'
    );

    expect($found->id)->toBe($user->id);
    expect($found->fresh()->phone)->toBe('+995555948217');
});

it('сверяет верификацию телефона независимо от маски с пробелами', function () {
    $verification = PhoneVerification::factory()->verified()->create([
        'phone' => '+995555123456',
    ]);

    postBreakfastOrder([
        'customer_phone' => '+995 555 12 34 56',
        'delivery_type' => 'delivery',
        'delivery_city' => 'Batumi',
        'delivery_street' => 'ул. Тестовая',
        'delivery_house' => '123',
        'verification_request_id' => $verification->request_id,
    ])->assertCreated();

    expect(Order::query()->first()->customer_phone)->toBe('+995555123456');
});
