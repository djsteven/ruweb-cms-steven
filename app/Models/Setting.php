<?php

namespace App\Models;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Crypt;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'options',
    ];

    protected $casts = [
        'options' => 'array',
    ];

    protected static array $cache = [];

    public static function get(string $key, mixed $default = null): mixed
    {
        if (array_key_exists($key, static::$cache)) {
            return static::$cache[$key];
        }

        $setting = static::where('key', $key)->first();

        if (! $setting) {
            return $default;
        }

        $value = static::castValue($setting->value, $setting->type);
        static::$cache[$key] = $value;

        return $value;
    }

    public static function set(string $key, mixed $value): void
    {
        $setting = static::where('key', $key)->first();

        if ($setting) {
            if ($setting->type === 'password' && is_string($value) && $value !== '') {
                $value = Crypt::encryptString($value);
            }
            $setting->update(['value' => $value]);
        }

        unset(static::$cache[$key]);
    }

    public static function getGroup(string $group): Collection
    {
        return static::where('group', $group)->get();
    }

    public static function allGrouped(): Collection
    {
        return static::all()->groupBy('group');
    }

    public static function clearCache(): void
    {
        static::$cache = [];
    }

    protected static function castValue(?string $value, string $type): mixed
    {
        return match ($type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $value,
            'media' => $value ? Media::find((int) $value) : null,
            'password' => static::decryptPassword($value),
            default => $value,
        };
    }

    protected static function decryptPassword(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        try {
            return Crypt::decryptString($value);
        } catch (DecryptException) {
            return null;
        }
    }

    public static function hasValue(string $key): bool
    {
        return (bool) static::where('key', $key)->value('value');
    }
}
