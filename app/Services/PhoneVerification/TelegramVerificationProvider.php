<?php

namespace App\Services\PhoneVerification;

use App\Enums\PhoneVerificationChannel;
use App\Enums\PhoneVerificationStatus;
use App\Models\PhoneVerification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TelegramVerificationProvider implements PhoneVerificationProviderInterface
{
    public const MESSAGE_TOKEN_INVALID_OR_EXPIRED = 'Токен верификации недействителен или истек. Пожалуйста, попробуйте снова на сайте.';

    public function send(string $phone): array
    {
        PhoneVerification::where('phone', $phone)
            ->where('channel', PhoneVerificationChannel::Telegram->value)
            ->where('status', PhoneVerificationStatus::Pending->value)
            ->update(['status' => PhoneVerificationStatus::Cancelled->value]);

        $requestId = 'tg-'.Str::uuid();
        $token = Str::random(48);

        PhoneVerification::create([
            'phone' => $phone,
            'request_id' => $requestId,
            'channel' => PhoneVerificationChannel::Telegram->value,
            'status' => PhoneVerificationStatus::Pending->value,
            'expires_at' => now()->addMinutes(10),
            'metadata' => [
                'telegram_token' => $token,
            ],
        ]);

        $botName = config('services.telegram.bot_name');
        $telegramUrl = $botName
            ? sprintf('https://t.me/%s?start=%s', $botName, $token)
            : null;

        return [
            'success' => true,
            'request_id' => $requestId,
            'telegram_url' => $telegramUrl,
            'telegram_token' => $token,
        ];
    }

    public function verify(string $requestId, ?string $code = null): array
    {
        $verification = PhoneVerification::where('request_id', $requestId)->first();

        if (! $verification) {
            return [
                'success' => false,
                'message' => 'Запрос верификации не найден',
            ];
        }

        if ($verification->isExpired()) {
            $verification->update(['status' => PhoneVerificationStatus::Expired->value]);

            return [
                'success' => false,
                'message' => 'Срок действия Telegram-верификации истёк',
            ];
        }

        if ($verification->verified) {
            return [
                'success' => true,
                'message' => 'Номер уже верифицирован',
                'phone' => $verification->phone,
            ];
        }

        return [
            'success' => false,
            'message' => 'Ожидаем подтверждение в Telegram',
        ];
    }

    public function cancel(string $requestId): bool
    {
        return (bool) PhoneVerification::where('request_id', $requestId)
            ->update(['status' => PhoneVerificationStatus::Cancelled->value]);
    }

    public function verifyByTelegramToken(string $token, ?int $telegramUserId = null): array
    {
        $token = trim($token);
        if ($token === '') {
            return [
                'success' => false,
                'message' => self::MESSAGE_TOKEN_INVALID_OR_EXPIRED,
            ];
        }

        // Поиск по токену в PHP по Telegram-записям — стабильно для любых драйверов БД (MySQL, PostgreSQL, SQLite).
        $verification = $this->findVerificationByTelegramTokenInPhp($token);

        if (! $verification) {
            $pending = PhoneVerification::where('channel', PhoneVerificationChannel::Telegram->value)
                ->where('status', PhoneVerificationStatus::Pending->value)
                ->orderByDesc('id')
                ->limit(3)
                ->get();
            Log::warning('phone.verify.telegram: token not found', [
                'token_length' => strlen($token),
                'token_received_preview' => strlen($token) >= 8 ? substr($token, 0, 8).'…'.substr($token, -8) : $token,
                'pending_count' => $pending->count(),
                'latest_pending_tokens' => $pending->map(fn ($v) => [
                    'id' => $v->id,
                    'request_id' => $v->request_id,
                    'stored_preview' => $this->tokenPreview($v->metadata['telegram_token'] ?? null),
                ])->toArray(),
            ]);

            return [
                'success' => false,
                'message' => self::MESSAGE_TOKEN_INVALID_OR_EXPIRED,
            ];
        }

        if ($verification->status === PhoneVerificationStatus::Verified->value) {
            return [
                'success' => true,
                'message' => 'Номер уже подтверждён. Вернитесь в форму заказа.',
                'request_id' => $verification->request_id,
                'phone' => $verification->phone,
            ];
        }

        if ($verification->status === PhoneVerificationStatus::Cancelled->value) {
            return [
                'success' => false,
                'message' => 'Ссылка устарела. Вернитесь на сайт и нажмите «Продолжить в Telegram» снова.',
            ];
        }

        if ($verification->isExpired()) {
            $verification->update(['status' => PhoneVerificationStatus::Expired->value]);

            return [
                'success' => false,
                'message' => self::MESSAGE_TOKEN_INVALID_OR_EXPIRED,
            ];
        }

        $metadata = $verification->metadata ?? [];
        if ($telegramUserId) {
            $metadata['telegram_user_id'] = $telegramUserId;
        }

        $verification->update([
            'verified' => true,
            'verified_at' => now(),
            'status' => PhoneVerificationStatus::Verified->value,
            'metadata' => $metadata,
        ]);

        return [
            'success' => true,
            'message' => 'Телефон подтвержден, заказ принят. Вернитесь в форму заказа и выберите доставку.',
            'request_id' => $verification->request_id,
            'phone' => $verification->phone,
        ];
    }

    /**
     * Поиск верификации по telegram_token среди записей канала Telegram.
     * Проверка токена в PHP, без JSON-path в SQL — одинаково работает во всех окружениях (MySQL, PostgreSQL, SQLite).
     */
    private function findVerificationByTelegramTokenInPhp(string $token): ?PhoneVerification
    {
        $token = trim($token);
        $candidates = PhoneVerification::where('channel', PhoneVerificationChannel::Telegram->value)->get();

        foreach ($candidates as $v) {
            $meta = $v->metadata;
            if (is_string($meta)) {
                $meta = json_decode($meta, true);
            }
            $stored = is_array($meta) ? ($meta['telegram_token'] ?? null) : null;
            if (is_string($stored) && trim($stored) === $token) {
                return $v;
            }
        }

        return null;
    }

    private function tokenPreview(?string $t): ?string
    {
        if ($t === null || $t === '') {
            return null;
        }

        return strlen($t) >= 8 ? substr($t, 0, 8).'…'.substr($t, -8) : $t;
    }
}
