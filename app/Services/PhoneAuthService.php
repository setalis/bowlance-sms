<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PhoneAuthService
{
    /**
     * Find existing user by phone or create a new one.
     * Phone is normalized (Georgia +995); existing user's name is never overwritten.
     */
    public function findOrCreateUser(string $phone, ?string $email, ?string $name): User
    {
        $normalizedPhone = PhoneNormalizer::normalize(trim($phone));
        if ($normalizedPhone === '') {
            $normalizedPhone = trim($phone);
        }

        $user = User::where('phone', $normalizedPhone)->first();

        if ($user) {
            Log::info('Existing user found by phone', [
                'user_id' => $user->id,
                'phone' => $normalizedPhone,
            ]);

            return $user;
        }

        $digitsOnly = preg_replace('/\D/', '', $normalizedPhone) ?: $normalizedPhone;
        if (empty($email)) {
            $email = $digitsOnly.'@bowlance.ge';
        }

        $password = Str::random(32);
        $userName = $name ?: 'Клиент '.$normalizedPhone;

        $user = User::create([
            'name' => $userName,
            'email' => $email,
            'phone' => $normalizedPhone,
            'password' => $password,
            'role' => UserRole::User,
        ]);

        Log::info('New user created via phone verification', [
            'user_id' => $user->id,
            'phone' => $normalizedPhone,
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

        $normalizedPhone = PhoneNormalizer::normalize(trim($phone));
        $targetUser = User::where('phone', $normalizedPhone)->first();

        // If no user exists with this phone, no conflict
        if (! $targetUser) {
            return [
                'should_reauth' => false,
                'target_user' => null,
            ];
        }

        // If the phone belongs to the currently logged in user, no conflict
        if ($targetUser->id === $currentUserId) {
            return [
                'should_reauth' => false,
                'target_user' => null,
            ];
        }

        // Different user - requires confirmation
        return [
            'should_reauth' => true,
            'target_user' => $targetUser,
        ];
    }
}
