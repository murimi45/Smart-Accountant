<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

/**
 * Tenant-scoped cache keys. Prefix every key with school_id before use.
 */
class TenantCache
{
    public static function key(string $key, ?int $schoolId = null): string
    {
        $schoolId ??= auth()->user()?->school_id;

        if (! $schoolId) {
            throw new \InvalidArgumentException('TenantCache requires a school_id context.');
        }

        return "school:{$schoolId}:{$key}";
    }

    public static function get(string $key, mixed $default = null, ?int $schoolId = null): mixed
    {
        return Cache::get(self::key($key, $schoolId), $default);
    }

    public static function put(string $key, mixed $value, mixed $ttl = null, ?int $schoolId = null): bool
    {
        return Cache::put(self::key($key, $schoolId), $value, $ttl);
    }

    public static function remember(string $key, mixed $ttl, callable $callback, ?int $schoolId = null): mixed
    {
        return Cache::remember(self::key($key, $schoolId), $ttl, $callback);
    }

    public static function forget(string $key, ?int $schoolId = null): bool
    {
        return Cache::forget(self::key($key, $schoolId));
    }

    /**
     * Flush all cached entries for a school. Requires a tag-capable driver (e.g. redis).
     */
    public static function flushSchool(?int $schoolId = null): void
    {
        $schoolId ??= auth()->user()?->school_id;

        if (! $schoolId) {
            return;
        }

        $store = Cache::getStore();

        if (method_exists($store, 'tags')) {
            Cache::tags(["school:{$schoolId}"])->flush();
        }
    }

    /** Tag name for drivers that support cache tags alongside prefixed keys. */
    public static function tag(?int $schoolId = null): string
    {
        $schoolId ??= auth()->user()?->school_id;

        return "school:{$schoolId}";
    }
}
