<?php

use App\Models\PhoneVerification;
use App\Models\Setting;
use App\Services\WoltDriveService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
});

function deliveryOrderPayload(array $overrides = []): array
{
    return array_merge([
        'customer_name' => 'Тестовый Клиент',
        'customer_phone' => '+995555123456',
        'customer_email' => 'test@example.com',
        'delivery_type' => 'delivery',
        'delivery_city' => 'Batumi',
        'delivery_street' => 'ул. Тестовая',
        'delivery_house' => '10',
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

it('blocks sms verification when phone verification integration is disabled', function () {
    Setting::set('phone_verification_enabled', false);

    $response = $this->postJson('/phone/verify/send', [
        'phone' => '+995555123456',
    ]);

    $response->assertForbidden();
    $response->assertJson([
        'success' => false,
        'message' => 'Верификация телефона временно недоступна',
    ]);
});

it('blocks telegram verification when phone verification integration is disabled', function () {
    Setting::set('phone_verification_enabled', false);

    $response = $this->postJson('/phone/verify/telegram/start', [
        'phone' => '+995555123456',
    ]);

    $response->assertForbidden();
    $response->assertJson([
        'success' => false,
        'message' => 'Верификация телефона временно недоступна',
    ]);
});

it('allows delivery order via callback when phone verification is disabled', function () {
    Setting::set('phone_verification_enabled', false);

    $response = $this->postJson('/orders', deliveryOrderPayload([
        'verification_method' => 'callback',
    ]));

    $response->assertCreated();
    $response->assertJsonPath('order.needs_callback', true);

    $this->assertDatabaseHas('orders', [
        'customer_phone' => '+995555123456',
        'needs_callback' => true,
        'phone_verified' => false,
    ]);
});

it('rejects sms verification method when phone verification is disabled', function () {
    Setting::set('phone_verification_enabled', false);

    $verification = PhoneVerification::factory()->verified()->create([
        'phone' => '+995555123456',
    ]);

    $response = $this->postJson('/orders', deliveryOrderPayload([
        'verification_method' => 'telegram',
        'verification_request_id' => $verification->request_id,
    ]));

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors('verification_method');
});

it('disables wolt drive service when admin setting is off', function () {
    config()->set('wolt.drive.enabled', true);
    config()->set('wolt.drive.token', 'test-token');
    config()->set('wolt.drive.mode', 'venueful');
    config()->set('wolt.drive.venue_id', 'test-venue-id');

    Setting::set('wolt_delivery_enabled', false);

    expect(app(WoltDriveService::class)->isEnabled())->toBeFalse();
});

it('creates delivery order without wolt when wolt integration is disabled', function () {
    $baseUrl = 'https://daas-public-api.development.dev.woltapi.com';
    $venueId = 'test-venue-id';

    config()->set('wolt.drive.enabled', true);
    config()->set('wolt.drive.base_url', $baseUrl);
    config()->set('wolt.drive.token', 'test-token');
    config()->set('wolt.drive.mode', 'venueful');
    config()->set('wolt.drive.venue_id', $venueId);

    Setting::set('wolt_delivery_enabled', false);

    Http::fake();

    $response = $this->postJson('/orders', deliveryOrderPayload([
        'verification_method' => 'callback',
    ]));

    $response->assertCreated();
    $response->assertJsonPath('order.wolt_delivery_id', null);

    Http::assertNothingSent();

    $this->assertDatabaseHas('orders', [
        'customer_phone' => '+995555123456',
        'wolt_delivery_id' => null,
    ]);
});

it('returns unavailable wolt estimate when wolt integration is disabled', function () {
    config()->set('wolt.drive.enabled', true);
    config()->set('wolt.drive.token', 'test-token');
    config()->set('wolt.drive.mode', 'venueful');
    config()->set('wolt.drive.venue_id', 'test-venue-id');

    Setting::set('wolt_delivery_enabled', false);

    Http::fake();

    $response = $this->postJson('/wolt/delivery-estimate', [
        'delivery_city' => 'Batumi',
        'delivery_street' => 'ул. Тестовая',
        'delivery_house' => '10',
    ]);

    $response->assertSuccessful();
    $response->assertJson([
        'success' => true,
        'available' => false,
    ]);

    Http::assertNothingSent();
});

it('keeps default integration settings enabled', function () {
    expect(Setting::get('phone_verification_enabled'))->toBeTrue();
    expect(Setting::get('wolt_delivery_enabled'))->toBeTrue();
});
