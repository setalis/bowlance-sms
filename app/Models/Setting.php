<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public $timestamps = true;

    private const CACHE_TTL = 600;

    public static function get(string $key, mixed $default = null): mixed
    {
        $cacheKey = 'setting_'.$key;

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($key, $default) {
            $row = static::query()->where('key', $key)->first();

            if ($row === null) {
                return $default;
            }

            return static::castValue($key, $row->value);
        });
    }

    public static function set(string $key, mixed $value): void
    {
        $value = is_bool($value) ? ($value ? '1' : '0') : (string) $value;

        static::query()->updateOrInsert(
            ['key' => $key],
            ['value' => $value, 'updated_at' => now()]
        );

        Cache::forget('setting_'.$key);
    }

    protected static function castValue(string $key, ?string $value): mixed
    {
        if ($value === null) {
            return null;
        }

        if ($key === 'orders_enabled') {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }

        return $value;
    }
}
