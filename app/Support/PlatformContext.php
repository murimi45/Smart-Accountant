<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;

/**
 * Explicit scope bypass for platform administrators. Always audit via PlatformAudit.
 */
class PlatformContext
{
    private static bool $active = false;

    private static ?int $actingSchoolId = null;

    public static function isActive(): bool
    {
        return self::$active;
    }

    public static function schoolId(): ?int
    {
        return self::$actingSchoolId;
    }

    /**
     * Run a callback with an explicit tenant context (uses withoutGlobalScopes at call sites).
     *
     * @template T
     * @param  callable(): T  $callback
     * @return T
     */
    public static function runAsPlatform(User $actor, int $schoolId, string $action, callable $callback): mixed
    {
        if (! $actor->isPlatformAdmin()) {
            throw new AuthorizationException('Platform administrator access required.');
        }

        PlatformAudit::log($actor, $action, $schoolId);

        self::$active = true;
        self::$actingSchoolId = $schoolId;

        try {
            return $callback();
        } finally {
            self::$active = false;
            self::$actingSchoolId = null;
        }
    }
}
