<?php

namespace App\Support;

class PhoneNumber
{
    public static function toE164(?string $phone): string
    {
        $digits = preg_replace('/\D+/', '', trim((string) $phone)) ?? '';

        if ($digits === '') {
            return '';
        }

        if (str_starts_with($digits, '995')) {
            $digits = substr($digits, 3);
        }

        $digits = ltrim($digits, '0');

        if (strlen($digits) === 9) {
            return '+995'.$digits;
        }

        return '';
    }

    /**
     * @return list<string>
     */
    public static function lookupCandidates(?string $phone): array
    {
        $canonical = self::toE164($phone);

        if ($canonical === '') {
            return array_values(array_unique(array_filter([
                trim((string) $phone),
                preg_replace('/\D+/', '', (string) $phone) ?: null,
            ])));
        }

        $digits = ltrim($canonical, '+');
        $localNine = substr($digits, 3);

        return array_values(array_unique(array_filter([
            $canonical,
            $digits,
            '0'.$localNine,
            $localNine,
            trim((string) $phone),
        ])));
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
