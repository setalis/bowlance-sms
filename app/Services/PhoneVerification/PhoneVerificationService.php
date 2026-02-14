<?php

namespace App\Services\PhoneVerification;

use App\Enums\PhoneVerificationChannel;
use App\Models\PhoneVerification;
use InvalidArgumentException;

class PhoneVerificationService
{
    public function __construct(
        protected SmsVerificationProvider $smsVerificationProvider,
        protected TelegramVerificationProvider $telegramVerificationProvider,
    ) {}

    public function send(string $phone, string $channel): array
    {
        return $this->provider($channel)->send($phone);
    }

    public function verify(string $requestId, ?string $code = null): array
    {
        $provider = $this->providerByRequestId($requestId);

        return $provider->verify($requestId, $code);
    }

    public function cancel(string $requestId): bool
    {
        $provider = $this->providerByRequestId($requestId);

        return $provider->cancel($requestId);
    }

    protected function provider(string $channel): PhoneVerificationProviderInterface
    {
        return match ($channel) {
            PhoneVerificationChannel::Sms->value => $this->smsVerificationProvider,
            PhoneVerificationChannel::Telegram->value => $this->telegramVerificationProvider,
            default => throw new InvalidArgumentException('Неподдерживаемый канал верификации'),
        };
    }

    protected function providerByRequestId(string $requestId): PhoneVerificationProviderInterface
    {
        $verification = PhoneVerification::query()
            ->select('channel')
            ->where('request_id', $requestId)
            ->first();

        if (! $verification) {
            return $this->smsVerificationProvider;
        }

        return $verification->channel === PhoneVerificationChannel::Telegram->value
            ? $this->telegramVerificationProvider
            : $this->smsVerificationProvider;
    }
}
