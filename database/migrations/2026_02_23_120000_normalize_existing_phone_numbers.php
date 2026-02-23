<?php

use App\Models\PhoneVerification;
use App\Models\User;
use App\Services\PhoneNormalizer;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Normalize existing phone numbers to E.164 (Georgia +995).
     * Skips users when normalization would create a duplicate (same normalized phone already exists).
     */
    public function up(): void
    {
        foreach (User::whereNotNull('phone')->get() as $user) {
            $normalized = PhoneNormalizer::normalize(trim($user->phone));
            if ($normalized === '' || $normalized === $user->phone) {
                continue;
            }
            $exists = User::where('phone', $normalized)->where('id', '!=', $user->id)->exists();
            if ($exists) {
                Log::warning('Phone normalization skipped: duplicate normalized phone', [
                    'user_id' => $user->id,
                    'current_phone' => $user->phone,
                    'normalized' => $normalized,
                ]);

                continue;
            }
            $user->update(['phone' => $normalized]);
        }

        foreach (PhoneVerification::all() as $verification) {
            $normalized = PhoneNormalizer::normalize(trim($verification->phone));
            if ($normalized !== '' && $normalized !== $verification->phone) {
                $verification->update(['phone' => $normalized]);
            }
        }
    }

    /**
     * Reverse is not reliable (original format lost).
     */
    public function down(): void
    {
        // No-op: we cannot restore original phone formats
    }
};
