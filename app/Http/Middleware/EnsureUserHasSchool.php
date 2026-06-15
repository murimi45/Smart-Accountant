<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasSchool
{
    /**
     * Tenant routes require a school assignment. Platform admins must not use tenant UI.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (! $user || empty($user->school_id)) {
            abort(403, 'Your account is not assigned to a school.');
        }

        return $next($request);
    }
}
