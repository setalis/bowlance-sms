<?php

namespace App\Services\PhoneVerification;

interface PhoneVerificationProviderInterface
{
    public function send(string $phone): array;

    public function verify(string $requestId, ?string $code = null): array;

    public function cancel(string $requestId): bool;
}
