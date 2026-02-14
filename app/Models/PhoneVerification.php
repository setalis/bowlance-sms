<?php

namespace App\Models;

use App\Enums\PhoneVerificationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhoneVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'phone',
        'request_id',
        'channel',
        'status',
        'code',
        'metadata',
        'verified',
        'verified_at',
        'expires_at',
        'attempts',
    ];

    protected function casts(): array
    {
        return [
            'verified' => 'boolean',
            'verified_at' => 'datetime',
            'expires_at' => 'datetime',
            'metadata' => 'array',
            'status' => 'string',
        ];
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function canAttempt(): bool
    {
        return $this->attempts < 3 && ! $this->isExpired();
    }

    public function markAsVerified(): void
    {
        $this->update([
            'verified' => true,
            'verified_at' => now(),
            'status' => PhoneVerificationStatus::Verified->value,
        ]);
    }

    public function incrementAttempts(): void
    {
        $this->increment('attempts');
    }
}
