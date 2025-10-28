<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cookie;

class TwoFactorController extends Controller
{
    protected Google2FA $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    // Show 2FA setup (QR + Code)
    public function showSetup(Request $request)
    {
        $user = $request->user();

        if (! $user) abort(403);

        $secret = $user->getTwoFactorSecret() ?? $this->google2fa->generateSecretKey();

        $company = config('app.name');
        $qrCode = $this->google2fa->getQRCodeInline($company, $user->email, $secret);

        $request->session()->put('two_factor_temp_secret', $secret);

        return view('twofactor.setup', compact('qrCode', 'secret'));
    }

    // Confirm and enable 2FA
    public function confirmSetup(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $user = $request->user();
        $secret = $request->session()->get('two_factor_temp_secret');

        if (! $secret) {
            return back()->withErrors(['code' => 'No 2FA setup in progress.']);
        }

        if (! $this->google2fa->verifyKey($secret, $request->code)) {
            return back()->withErrors(['code' => 'Invalid verification code.']);
        }

        // generate recovery codes
        $recoveryCodes = collect(range(1, 8))->map(fn () => Str::random(10))->toArray();

        $user->setTwoFactorSecret($secret);
        $user->setTwoFactorRecoveryCodes($recoveryCodes);
        $user->enableTwoFactor();

        activity()
            ->causedBy($user)
            ->performedOn($user)
            ->withProperties(['ip' => request()->ip()])
            ->log('Enabled two-factor authentication');

        $request->session()->forget('two_factor_temp_secret');

        return view('twofactor.recovery', ['codes' => $recoveryCodes]);
    }

    // Show login challenge screen
    public function showChallenge()
    {
        // If user is already session-verified, redirect intended
        if (session('two_factor_passed')) {
            return redirect()->intended('/');
        }

        return view('twofactor.challenge');
    }

    // Verify challenge at login (with optional trust device checkbox)
    public function verifyChallenge(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'trust_device' => 'sometimes|in:on'
        ]);

        $user = $request->user();
        if (! $user) abort(403);

        $entered = $request->code;
        $secret = $user->getTwoFactorSecret();

        $isValid = $secret && $this->google2fa->verifyKey($secret, $entered);

        // Check recovery codes if google code fails
        if (! $isValid) {
            $codes = $user->getTwoFactorRecoveryCodes();
            if (in_array($entered, $codes)) {
                $remaining = array_values(array_diff($codes, [$entered]));
                $user->setTwoFactorRecoveryCodes($remaining);
                $isValid = true;
            }
        }

        if (! $isValid) {
            // LOG FAILED ATTEMPT
            activity()
                ->causedBy($user)
                ->performedOn($user)
                ->withProperties(['ip' => request()->ip()])
                ->log('Invalid two-factor authentication code');

            return back()->withErrors(['code' => 'Invalid code.']);
        }

        // LOG SUCCESSFUL ATTEMPT
        activity()
            ->causedBy($user)
            ->performedOn($user)
            ->withProperties(['ip' => request()->ip()])
            ->log('2FA challenge passed');

        // Mark 2FA passed in session
        session()->put('two_factor_passed', true);

        // If user asked to trust device, set trusted cookie (30 days)
        if ($request->has('trust_device')) {
            $minutes = 60 * 24 * 30; // 30 days
            $payload = [
                'user_id' => $user->id,
                'ua' => substr($request->header('User-Agent') ?? '', 0, 500), // truncated UA
                // token ties cookie to user + user agent + secret
                'token' => hash_hmac('sha256', $user->id . '|' . ($request->header('User-Agent') ?? '') . '|' . $user->getTwoFactorSecret(), config('app.key')),
                'created_at' => now()->toDateTimeString(),
            ];
            $cookie = cookie('trusted_device', encrypt(json_encode($payload)), $minutes);
            // redirect with cookie attached
            return redirect()->intended('/')->withCookie($cookie);
        }

        // Normal redirect (intended)
        return redirect()->intended('/');
    }

    // Disable 2FA
    public function disable(Request $request)
    {
        $user = $request->user();
        // ideally confirm password here
        $user->disableTwoFactor();

        activity()
            ->causedBy($user)
            ->performedOn($user)
            ->withProperties(['ip' => request()->ip()])
            ->log('Disabled two-factor authentication');

        // remove trusted_device cookie if present
        $expired = Cookie::forget('trusted_device');

        return redirect()->back()->withCookie($expired)->with('status', 'Two-factor disabled');
    }
}
