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
     * Find existing user by phone or create a new one
     */
    public function findOrCreateUser(string $phone, ?string $email, ?string $name): User
    {
        // Normalize phone (remove + sign for internal use)
        $normalizedPhone = ltrim($phone, '+');

        // Try to find existing user by phone
        $user = User::where('phone', $phone)->first();

        if ($user) {
            Log::info('Existing user found by phone', [
                'user_id' => $user->id,
                'phone' => $phone,
            ]);

            return $user;
        }

        // Generate email if not provided
        if (empty($email)) {
            $email = $normalizedPhone.'@bowlance.ge';
        }

        // Generate random secure password
        $password = Str::random(32);

        // Use provided name or generate default
        $userName = $name ?: 'Клиент '.$phone;

        // Create new user
        $user = User::create([
            'name' => $userName,
            'email' => $email,
            'phone' => $phone,
            'password' => $password,
            'role' => UserRole::User,
        ]);

        Log::info('New user created via phone verification', [
            'user_id' => $user->id,
            'phone' => $phone,
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
        // If no one is logged in, no need to re-authenticate
        if (! $currentUserId) {
            return [
                'should_reauth' => false,
                'target_user' => null,
            ];
        }

        // Find user by phone
        $targetUser = User::where('phone', $phone)->first();

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
