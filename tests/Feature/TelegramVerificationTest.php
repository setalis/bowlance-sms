<?php

use App\Models\PhoneVerification;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

    config([
        'telegram.bot_token' => 'test-bot-token',
        'telegram.bot_username' => 'test_bot',
        'telegram.webhook_secret' => '',
    ]);
});

it('инициирует верификацию через Telegram и возвращает ссылку', function () {
    $response = $this->postJson('/phone/verify/telegram/start', [
        'phone' => '+995555123456',
    ]);

    $response->assertSuccessful();
    $response->assertJsonStructure([
        'success',
        'message',
        'request_id',
        'telegram_link',
    ]);
    $response->assertJson(['success' => true]);

    $data = $response->json();
    expect($data['telegram_link'])->toContain('t.me/test_bot');
    expect($data['telegram_link'])->toContain('start=');

    $this->assertDatabaseHas('phone_verifications', [
        'phone' => '+995555123456',
        'channel' => 'telegram',
        'verified' => false,
    ]);
});

it('возвращает ошибку при невалидном номере для Telegram', function () {
    $response = $this->postJson('/phone/verify/telegram/start', [
        'phone' => 'invalid',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors('phone');
});

it('нормализует номер при старте Telegram-верификации', function () {
    $response = $this->postJson('/phone/verify/telegram/start', [
        'phone' => '995555123456',
    ]);

    $response->assertSuccessful();

    $this->assertDatabaseHas('phone_verifications', [
        'phone' => '+995555123456',
        'channel' => 'telegram',
    ]);
});

it('верифицирует телефон через Telegram по коду из БД', function () {
    $verification = PhoneVerification::create([
        'phone' => '+995555123456',
        'channel' => 'telegram',
        'request_id' => 'tg-test-request-id',
        'telegram_token' => 'test-tg-token',
        'telegram_chat_id' => 123456789,
        'code' => '654321',
        'expires_at' => now()->addMinutes(10),
        'verified' => false,
    ]);

    Http::fake([
        'api.telegram.org/*' => Http::response(['ok' => true], 200),
    ]);

    $response = $this->postJson('/phone/verify/check', [
        'request_id' => 'tg-test-request-id',
        'code' => '654321',
    ]);

    $response->assertSuccessful();
    $response->assertJson(['success' => true]);

    $verification->refresh();
    expect($verification->verified)->toBeTrue();
    expect($verification->verified_at)->not->toBeNull();
});

it('отклоняет неверный код для Telegram-верификации', function () {
    PhoneVerification::create([
        'phone' => '+995555123456',
        'channel' => 'telegram',
        'request_id' => 'tg-test-request-id',
        'telegram_token' => 'test-tg-token',
        'code' => '654321',
        'expires_at' => now()->addMinutes(10),
        'verified' => false,
    ]);

    $response = $this->postJson('/phone/verify/check', [
        'request_id' => 'tg-test-request-id',
        'code' => '000000',
    ]);

    $response->assertStatus(422);
    $response->assertJson(['success' => false]);
});

it('обрабатывает Telegram webhook с командой /start TOKEN', function () {
    Http::fake([
        'api.telegram.org/*' => Http::response(['ok' => true], 200),
    ]);

    PhoneVerification::create([
        'phone' => '+995555123456',
        'channel' => 'telegram',
        'request_id' => 'tg-webhook-test',
        'telegram_token' => 'webhook-test-token',
        'expires_at' => now()->addMinutes(10),
        'verified' => false,
    ]);

    $update = [
        'update_id' => 1,
        'message' => [
            'message_id' => 100,
            'chat' => ['id' => 777111222, 'type' => 'private'],
            'text' => '/start webhook-test-token',
        ],
    ];

    $response = $this->postJson('/telegram/webhook', $update);

    $response->assertSuccessful();
    $response->assertJson(['ok' => true]);
});

it('обрабатывает Telegram webhook с callback_query confirm:TOKEN', function () {
    Http::fake([
        'api.telegram.org/*' => Http::response(['ok' => true], 200),
    ]);

    $verification = PhoneVerification::create([
        'phone' => '+995555123456',
        'channel' => 'telegram',
        'request_id' => 'tg-callback-test',
        'telegram_token' => 'callback-test-token',
        'expires_at' => now()->addMinutes(10),
        'verified' => false,
    ]);

    $update = [
        'update_id' => 2,
        'callback_query' => [
            'id' => 'cq-id-123',
            'from' => ['id' => 777111333],
            'message' => [
                'message_id' => 101,
                'chat' => ['id' => 777111333],
            ],
            'data' => 'confirm:callback-test-token',
        ],
    ];

    $response = $this->postJson('/telegram/webhook', $update);

    $response->assertSuccessful();
    $response->assertJson(['ok' => true]);

    $verification->refresh();
    expect($verification->code)->not->toBeNull();
    expect($verification->telegram_chat_id)->toBe(777111333);
});

it('отклоняет Telegram webhook при неверном секрете', function () {
    config(['telegram.webhook_secret' => 'super-secret']);

    $update = ['update_id' => 99, 'message' => []];

    $response = $this->postJson('/telegram/webhook', $update, [
        'X-Telegram-Bot-Api-Secret-Token' => 'wrong-secret',
    ]);

    $response->assertStatus(403);
});

it('принимает Telegram webhook при правильном секрете', function () {
    config(['telegram.webhook_secret' => 'correct-secret']);

    Http::fake([
        'api.telegram.org/*' => Http::response(['ok' => true], 200),
    ]);

    $update = [
        'update_id' => 100,
        'message' => [
            'message_id' => 110,
            'chat' => ['id' => 99999, 'type' => 'private'],
            'text' => '/start',
        ],
    ];

    $response = $this->postJson('/telegram/webhook', $update, [
        'X-Telegram-Bot-Api-Secret-Token' => 'correct-secret',
    ]);

    $response->assertSuccessful();
    $response->assertJson(['ok' => true]);
});
