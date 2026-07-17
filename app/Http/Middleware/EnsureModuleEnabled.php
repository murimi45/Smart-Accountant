<?php

namespace App\Http\Middleware;

use App\Core\Modules\ModuleRegistry;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureModuleEnabled
{
    public function handle(Request $request, Closure $next, string $slug): Response
    {
        $user = Auth::user();

        if (! $user || empty($user->school_id)) {
            abort(403, 'Your account is not assigned to a school.');
        }

        if (! ModuleRegistry::schoolHas((int) $user->school_id, $slug)) {
            abort(403, "This school does not have the [{$slug}] module enabled.");
        }

        return $next($request);
    }
}
