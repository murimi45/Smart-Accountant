<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class PlatformAudit
{
    /**
     * @param  array<string, mixed>  $meta
     */
    public static function log(User $actor, string $action, ?int $schoolId = null, array $meta = []): void
    {
        Log::channel('security')->info('platform.action', [
            'actor_id'    => $actor->id,
            'actor_email' => $actor->email,
            'action'      => $action,
            'school_id'   => $schoolId,
            'path'        => request()?->path(),
            'ip'          => request()?->ip(),
            'meta'        => $meta,
        ]);
    }
}
