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

        // If user does not have 2FA enabled, allow through
        if (! $user->two_factor_enabled) {
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
                    // Recompute expected token using current user agent + stored secret
                    $currentUa = $request->header('User-Agent') ?? '';
                    $expected = hash_hmac('sha256', $user->id . '|' . $currentUa . '|' . $user->getTwoFactorSecret(), config('app.key'));

                    if (hash_equals($expected, $payload['token'])) {
                        // Trust verified for this device — set session and continue
                        session()->put('two_factor_passed', true);
                        return $next($request);
                    }
                }
            } catch (\Exception $e) {
                // invalid cookie / decrypt failed - ignore and continue to challenge
            }
        }

        // Otherwise redirect to challenge
        return redirect()->route('twofactor.challenge');
    }
}
