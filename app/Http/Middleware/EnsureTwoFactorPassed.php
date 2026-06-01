<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureTwoFactorPassed
{

    public function handle(Request $request, Closure $next)
{
    $user = Auth::user();

    // If no user, redirect to login
    if (! $user) {
        return redirect()->route('login');
    }

    // Allow access to 2FA setup/confirm routes during initial setup
    if ($request->routeIs('twofactor.setup') || $request->routeIs('twofactor.confirm')) {
        return $next($request);
    }

    // If user is sensitive role but hasn't enabled 2FA yet, force setup
    if ($user->isSensitiveRole() && !$user->two_factor_enabled) {
        return redirect()->route('twofactor.setup')
            ->with('warning', 'You must enable two-factor authentication to continue.');
    }

    // If user doesn't have 2FA enabled (and doesn't need it), allow through
    if (!$user->two_factor_enabled) {
        return $next($request);
    }

    // If session shows 2FA passed, allow
    if (session('two_factor_passed')) {
        return $next($request);
    }

    // Check trusted_device cookie
    $cookie = $request->cookie('trusted_device');
    if ($cookie) {
        try {
            $payload = json_decode(decrypt($cookie), true);
            if (is_array($payload)
                && isset($payload['user_id'], $payload['ua'], $payload['token'])
                && $payload['user_id'] == $user->id
            ) {
                $currentUa = $request->header('User-Agent') ?? '';
                $expected = hash_hmac('sha256', $user->id . '|' . $currentUa . '|' . $user->getTwoFactorSecret(), config('app.key'));

                if (hash_equals($expected, $payload['token'])) {
                    session()->put('two_factor_passed', true);
                    return $next($request);
                }
            }
        } catch (\Exception $e) {
            // invalid cookie
        }
    }

    // Otherwise redirect to challenge
    return redirect()->route('twofactor.challenge');
}
}
