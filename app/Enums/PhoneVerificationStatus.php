<?php

namespace App\Enums;

enum PhoneVerificationStatus: string
{
    case Pending = 'pending';
    case Verified = 'verified';
    case Expired = 'expired';
    case Failed = 'failed';
    case Cancelled = 'cancelled';
}
