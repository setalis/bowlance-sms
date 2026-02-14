<?php

namespace App\Services\PhoneVerification;

use App\Services\VonageVerifyService;

class SmsVerificationProvider implements PhoneVerificationProviderInterface
{
    public function __construct(
        protected VonageVerifyService $vonageVerifyService
    ) {}

    public function send(string $phone): array
    {
        return $this->vonageVerifyService->sendVerificationCode($phone);
    }

    public function verify(string $requestId, ?string $code = null): array
    {
        if (! $code) {
            return [
                'success' => false,
                'message' => 'Необходимо ввести код верификации',
            ];
        }

        return $this->vonageVerifyService->verifyCode($requestId, $code);
    }

    public function cancel(string $requestId): bool
    {
        return $this->vonageVerifyService->cancelVerification($requestId);
    }
}
