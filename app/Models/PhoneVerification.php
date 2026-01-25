<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhoneVerification extends Model
{
    use HasFactory;
    protected $fillable = [
        'phone',
        'request_id',
        'code',
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
        ]);
    }

    public function incrementAttempts(): void
    {
        $this->increment('attempts');
    }
}
