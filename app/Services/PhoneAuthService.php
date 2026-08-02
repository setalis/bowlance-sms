<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\User;
use App\Support\PhoneNumber;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PhoneAuthService
{
    /**
     * Find existing user by phone or create a new one
     */
    public function findOrCreateUser(string $phone, ?string $email, ?string $name): User
    {
        $canonicalPhone = PhoneNumber::toE164($phone);

        $user = User::query()
            ->whereIn('phone', PhoneNumber::lookupCandidates($canonicalPhone))
            ->first();

        if ($user) {
            if ($user->phone !== $canonicalPhone) {
                $user->update(['phone' => $canonicalPhone]);
            }

            Log::info('Existing user found by phone', [
                'user_id' => $user->id,
                'phone' => $canonicalPhone,
            ]);

            return $user->fresh();
        }

        if (empty($email)) {
            $email = ltrim($canonicalPhone, '+').'@bowlance.ge';
        }

        $password = Str::random(32);
        $userName = $name ?: 'Клиент '.$canonicalPhone;

        $user = User::create([
            'name' => $userName,
            'email' => $email,
            'phone' => $canonicalPhone,
            'password' => $password,
            'role' => UserRole::User,
        ]);

        Log::info('New user created via phone verification', [
            'user_id' => $user->id,
            'phone' => $canonicalPhone,
            'email' => $email,
        ]);

        return $user;
    }

    /**
     * Authenticate the user
     */
    public function authenticateUser(User $user): void
    {
        Auth::login($user);

        Log::info('User authenticated via phone verification', [
            'user_id' => $user->id,
            'phone' => $user->phone,
        ]);
    }

    /**
     * Check if re-authentication is needed (user switching scenario)
     */
    public function shouldReauthenticate(?int $currentUserId, string $phone): array
    {
        if (! $currentUserId) {
            return [
                'should_reauth' => false,
                'target_user' => null,
            ];
        }

        $targetUser = User::query()
            ->whereIn('phone', PhoneNumber::lookupCandidates($phone))
            ->first();

        if (! $targetUser) {
            return [
                'should_reauth' => false,
                'target_user' => null,
            ];
        }

        if ($targetUser->id === $currentUserId) {
            return [
                'should_reauth' => false,
                'target_user' => null,
            ];
        }

        return [
            'should_reauth' => true,
            'target_user' => $targetUser,
        ];
    }
}
