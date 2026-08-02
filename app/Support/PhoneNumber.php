<?php

namespace App\Support;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumber as ParsedNumber;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

class PhoneNumber
{
    public const DEFAULT_REGION = 'GE';

    public static function toE164(?string $phone, string $defaultRegion = self::DEFAULT_REGION): string
    {
        $parsed = self::parse($phone, $defaultRegion);

        return $parsed instanceof ParsedNumber
            ? self::util()->format($parsed, PhoneNumberFormat::E164)
            : '';
    }

    public static function isValid(?string $phone, string $defaultRegion = self::DEFAULT_REGION): bool
    {
        return self::parse($phone, $defaultRegion) instanceof ParsedNumber;
    }

    public static function formatDisplay(?string $phone, string $defaultRegion = self::DEFAULT_REGION): string
    {
        $parsed = self::parse($phone, $defaultRegion);

        return $parsed instanceof ParsedNumber
            ? self::util()->format($parsed, PhoneNumberFormat::INTERNATIONAL)
            : '';
    }

    public static function regionCode(?string $phone, string $defaultRegion = self::DEFAULT_REGION): ?string
    {
        $parsed = self::parse($phone, $defaultRegion);

        return $parsed instanceof ParsedNumber
            ? self::util()->getRegionCodeForNumber($parsed)
            : null;
    }

    /**
     * Значения, под которыми номер мог быть сохранён в базе до перехода на E.164.
     *
     * @return list<string>
     */
    public static function lookupCandidates(?string $phone, string $defaultRegion = self::DEFAULT_REGION): array
    {
        $raw = trim((string) $phone);
        $parsed = self::parse($phone, $defaultRegion);

        if (! $parsed instanceof ParsedNumber) {
            return self::uniqueValues([$raw, self::digits($raw)]);
        }

        $e164 = self::util()->format($parsed, PhoneNumberFormat::E164);
        $national = self::util()->format($parsed, PhoneNumberFormat::NATIONAL);

        return self::uniqueValues([
            $e164,
            ltrim($e164, '+'),
            $national,
            self::digits($national),
            $raw,
        ]);
    }

    private static function parse(?string $phone, string $defaultRegion): ?ParsedNumber
    {
        $raw = trim((string) $phone);

        if ($raw === '') {
            return null;
        }

        try {
            $parsed = self::util()->parse($raw, $defaultRegion);
        } catch (NumberParseException) {
            return null;
        }

        return self::util()->isValidNumber($parsed) ? $parsed : null;
    }

    private static function digits(string $value): string
    {
        return preg_replace('/\D+/', '', $value) ?? '';
    }

    /**
     * @param  list<string>  $values
     * @return list<string>
     */
    private static function uniqueValues(array $values): array
    {
        return array_values(array_unique(array_filter($values, fn (string $value): bool => $value !== '')));
    }

    private static function util(): PhoneNumberUtil
    {
        return PhoneNumberUtil::getInstance();
    }
}
