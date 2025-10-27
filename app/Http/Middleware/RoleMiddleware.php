<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
public function handle(Request $request, Closure $next, ...$roles)
{
    // Roles from middleware (e.g. ["admin", "accountant"])
    $allowedRoles = array_map('strtolower', $roles);

    // Logged-in user role
    $userRole = strtolower(Auth::user()->role);

    
    if (!in_array($userRole, $allowedRoles)) {
        abort(403, 'Unauthorized access.');
    }

    return $next($request);
}



}