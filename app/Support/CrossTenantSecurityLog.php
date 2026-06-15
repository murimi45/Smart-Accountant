<?php

namespace App\Support;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Log;

class CrossTenantSecurityLog
{
    public static function enabled(): bool
    {
        return (bool) config('tenant.security_log', true);
    }

    /**
     * @param  array<string, mixed>  $context
     */
    public static function attempt(string $type, array $context = []): void
    {
        if (! self::enabled()) {
            return;
        }

        Log::channel('security')->warning("cross_tenant.{$type}", self::baseContext($context));
    }

    public static function authorizationDenied(AuthorizationException $exception): void
    {
        self::attempt('authorization_denied', [
            'message' => $exception->getMessage(),
        ]);
    }

    /**
     * @param  class-string  $modelClass
     * @param  array<int|string>  $ids
     */
    public static function bulkIdMismatch(string $modelClass, array $ids): void
    {
        self::attempt('bulk_id_mismatch', [
            'model' => class_basename($modelClass),
            'ids'   => array_values($ids),
        ]);
    }

    /**
     * @param  class-string  $modelClass
     */
    public static function routeBindingProbe(string $modelClass, int|string $id): void
    {
        self::attempt('route_binding_probe', [
            'model'    => class_basename($modelClass),
            'target_id'=> $id,
        ]);
    }

    public static function roleDenied(string $requiredRoles): void
    {
        self::attempt('role_denied', [
            'required_roles' => $requiredRoles,
        ]);
    }

    /**
     * @param  array<string, mixed>  $context
     * @return array<string, mixed>
     */
    private static function baseContext(array $context): array
    {
        $request = request();
        $user = auth()->user();

        return array_merge([
            'user_id'   => $user?->id,
            'school_id' => $user?->school_id,
            'role'      => $user?->role,
            'route'     => $request?->route()?->getName(),
            'path'      => $request?->path(),
            'method'    => $request?->method(),
            'ip'        => $request?->ip(),
        ], $context);
    }
}
