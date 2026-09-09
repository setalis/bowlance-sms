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

function pickupOrderPayload(array $overrides = []): array
{
    return array_merge([
        'customer_name' => 'Тестовый Клиент',
        'customer_phone' => '+995555123456',
        'delivery_type' => DeliveryType::Pickup->value,
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

it('rejects an order without personal data consent', function (?bool $consent) {
    $payload = pickupOrderPayload();

    if ($consent !== null) {
        $payload['personal_data_consent'] = $consent;
    }

    $this->postJson('/orders', $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors('personal_data_consent');
})->with([
    'missing' => [null],
    'unchecked' => [false],
]);

it('creates a pickup order when personal data consent is accepted', function () {
    $response = $this->postJson('/orders', pickupOrderPayload([
        'personal_data_consent' => true,
    ]));

    $response->assertCreated();

    expect(Order::query()->count())->toBe(1);
});
