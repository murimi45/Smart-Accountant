<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

/**
 * Tenant-scoped file paths under storage/app/schools/{school_id}/...
 */
class TenantStorage
{
    public static function schoolId(?int $schoolId = null): int
    {
        $schoolId ??= auth()->user()?->school_id;

        if (! $schoolId) {
            throw new \InvalidArgumentException('TenantStorage requires a school_id context.');
        }

        return (int) $schoolId;
    }

    public static function path(string $relative, ?int $schoolId = null): string
    {
        return 'schools/'.self::schoolId($schoolId).'/'.ltrim($relative, '/');
    }

    public static function absolutePath(string $relative, ?int $schoolId = null): string
    {
        return storage_path('app/'.self::path($relative, $schoolId));
    }

    public static function ensureDirectory(string $relative, ?int $schoolId = null): void
    {
        $directory = dirname(self::absolutePath($relative, $schoolId));

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
    }

    public static function disk(string $name = 'local')
    {
        return Storage::disk($name);
    }

    public static function put(string $relative, mixed $contents, ?int $schoolId = null): string
    {
        $path = self::path($relative, $schoolId);
        self::disk()->put($path, $contents);

        return $path;
    }

    public static function delete(string $relative, ?int $schoolId = null): bool
    {
        return self::disk()->delete(self::path($relative, $schoolId));
    }
}
