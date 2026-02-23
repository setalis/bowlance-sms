<?php

use App\Models\PhoneVerification;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

    config([
        'vonage.api_key' => 'test_key',
        'vonage.api_secret' => 'test_secret',
        'vonage.test_mode' => true, // Включаем тестовый режим
    ]);
});

it('может отправить код верификации на телефон', function () {
    Http::fake([
        'api.nexmo.com/v2/verify' => Http::response([
            'request_id' => 'test-request-id-123',
        ], 200),
    ]);

    $response = $this->postJson('/phone/verify/send', [
        'phone' => '+995555123456',
    ]);

    $response->assertSuccessful();
    $response->assertJsonFragment(['success' => true]);
    $response->assertJsonStructure(['request_id']);

    $data = $response->json();
    $this->assertDatabaseHas('phone_verifications', [
        'phone' => '+995555123456',
        'request_id' => $data['request_id'],
        'verified' => false,
    ]);
});

it('возвращает ошибку при невалидном номере телефона', function () {
    $response = $this->postJson('/phone/verify/send', [
        'phone' => 'invalid',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors('phone');
});

it('может проверить код верификации', function () {
    $verification = PhoneVerification::create([
        'phone' => '+995555123456',
        'request_id' => 'test-request-id',
        'expires_at' => now()->addMinutes(5),
        'verified' => false,
    ]);

    Http::fake([
        'api.nexmo.com/v2/verify/*' => Http::response([], 200),
    ]);

    $response = $this->postJson('/phone/verify/check', [
        'request_id' => 'test-request-id',
        'code' => '123456',
    ]);

    $response->assertSuccessful();
    $response->assertJson([
        'success' => true,
    ]);

    $verification->refresh();
    expect($verification->verified)->toBeTrue();
    expect($verification->verified_at)->not->toBeNull();
});

it('возвращает ошибку при неверном коде верификации', function () {
    config(['vonage.test_mode' => false]);

    PhoneVerification::create([
        'phone' => '+995555123456',
        'request_id' => 'test-request-id',
        'expires_at' => now()->addMinutes(5),
        'verified' => false,
    ]);

    Http::fake([
        'api.nexmo.com/v2/verify/*' => Http::response([
            'title' => 'Invalid Code',
        ], 400),
    ]);

    $response = $this->postJson('/phone/verify/check', [
        'request_id' => 'test-request-id',
        'code' => '000000',
    ]);

    $response->assertStatus(422);
    $response->assertJson([
        'success' => false,
    ]);
});

it('ограничивает количество попыток проверки', function () {
    $verification = PhoneVerification::create([
        'phone' => '+995555123456',
        'request_id' => 'test-request-id',
        'expires_at' => now()->addMinutes(5),
        'verified' => false,
        'attempts' => 3,
    ]);

    $response = $this->postJson('/phone/verify/check', [
        'request_id' => 'test-request-id',
        'code' => '123456',
    ]);

    $response->assertStatus(422);
    $response->assertJsonFragment([
        'message' => 'Превышено количество попыток или истёк срок действия кода',
    ]);
});

it('не позволяет проверить код после истечения срока', function () {
    PhoneVerification::create([
        'phone' => '+995555123456',
        'request_id' => 'test-request-id',
        'expires_at' => now()->subMinutes(1),
        'verified' => false,
    ]);

    $response = $this->postJson('/phone/verify/check', [
        'request_id' => 'test-request-id',
        'code' => '123456',
    ]);

    $response->assertStatus(422);
    $response->assertJsonFragment([
        'message' => 'Превышено количество попыток или истёк срок действия кода',
    ]);
});

it('может отменить запрос верификации', function () {
    Http::fake([
        'api.nexmo.com/v2/verify/*' => Http::response([], 200),
    ]);

    $response = $this->postJson('/phone/verify/cancel', [
        'request_id' => 'test-request-id',
    ]);

    $response->assertSuccessful();
    $response->assertJson([
        'success' => true,
    ]);
});

it('нормализует номер телефона при отправке кода', function () {
    Http::fake([
        'api.nexmo.com/v2/verify' => Http::response([
            'request_id' => 'test-request-id-123',
        ], 200),
    ]);

    $response = $this->postJson('/phone/verify/send', [
        'phone' => '995555123456',
    ]);

    $response->assertSuccessful();

    $this->assertDatabaseHas('phone_verifications', [
        'phone' => '+995555123456',
    ]);
});

it('нормализует номер с пробелами и дефисами при отправке кода', function () {
    Http::fake([
        'api.nexmo.com/v2/verify' => Http::response([
            'request_id' => 'test-request-id-456',
        ], 200),
    ]);

    $response = $this->postJson('/phone/verify/send', [
        'phone' => '995 555 123 456',
    ]);

    $response->assertSuccessful();
    $this->assertDatabaseHas('phone_verifications', [
        'phone' => '+995555123456',
    ]);
});
