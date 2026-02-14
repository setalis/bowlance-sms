<?php

namespace App\Enums;

enum PhoneVerificationChannel: string
{
    case Sms = 'sms';
    case Telegram = 'telegram';
}
