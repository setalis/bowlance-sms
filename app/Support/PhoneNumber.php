<?php

namespace App\Support;

class PhoneNumber
{
    public static function toE164(?string $phone): string
    {
        $phone = trim((string) $phone);

        if ($phone === '') {
            return '';
        }

        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        if ($digits === '') {
            return '';
        }

        if (str_starts_with($digits, '995') && strlen($digits) >= 12) {
            return '+'.$digits;
        }

        if (str_starts_with($digits, '0') && strlen($digits) === 10) {
            return '+995'.substr($digits, 1);
        }

        if (strlen($digits) === 9) {
            return '+995'.$digits;
        }

        if (str_starts_with($phone, '+')) {
            return '+'.$digits;
        }

        return '+'.$digits;
    }

    /**
     * @return list<string>
     */
    public static function lookupCandidates(?string $phone): array
    {
        $canonical = self::toE164($phone);

        if ($canonical === '') {
            return [];
        }

        $digits = ltrim($canonical, '+');
        $candidates = [$canonical, $digits];

        if (str_starts_with($digits, '995') && strlen($digits) === 12) {
            $localNine = substr($digits, 3);
            $candidates[] = '0'.$localNine;
            $candidates[] = $localNine;
        }

        return array_values(array_unique($candidates));
    }

    public static function formatDisplay(?string $phone): string
    {
        $canonical = self::toE164($phone);

        if ($canonical === '' || ! preg_match('/^\+995(\d{9})$/', $canonical, $matches)) {
            return $canonical;
        }

        $local = $matches[1];

        return '+995 '.substr($local, 0, 3).' '.substr($local, 3, 2).' '.substr($local, 5, 2).' '.substr($local, 7, 2);
    }
}
