<?php

namespace App\Services;

class PhoneNormalizer
{
    /**
     * Normalize phone to E.164 format (priority: Georgia +995).
     * Removes spaces, dashes; for 9 digits starting with 5 or 10 digits starting with 0, prepends 995.
     */
    public static function normalize(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);

        if ($digits === '') {
            return '';
        }

        // Georgia: 9 digits starting with 5 (mobile) -> add 995
        if (strlen($digits) === 9 && $digits[0] === '5') {
            $digits = '995'.$digits;
        }

        // Georgia: 10 digits starting with 0 (e.g. 0555123456) -> replace leading 0 with 995
        if (strlen($digits) === 10 && $digits[0] === '0') {
            $digits = '995'.substr($digits, 1);
        }

        return '+'.$digits;
    }
}
