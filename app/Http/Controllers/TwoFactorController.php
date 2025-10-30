<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cookie;
use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

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

    if (! $user) {
        abort(403);
    }

    $google2fa = new Google2FA();

    // Generate secret
    $secret = $user->getTwoFactorSecret() ?? $google2fa->generateSecretKey();

    // Create the QR code URL (the URI that authenticator apps use)
    $company = config('app.name');
    $qrCodeUrl = $google2fa->getQRCodeUrl(
        $company,
        $user->email,
        $secret
    );

    // Generate the actual QR code image (SVG)
    $renderer = new ImageRenderer(
        new RendererStyle(200),
        new SvgImageBackEnd()
    );
    $writer = new Writer($renderer);
    $qrCodeSvg = $writer->writeString($qrCodeUrl);

    // Store the secret temporarily until confirmation
    $request->session()->put('two_factor_temp_secret', $secret);

    return view('twofactor.setup', [
        'qrCodeSvg' => $qrCodeSvg,
        'secret' => $secret,
    ]);
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

    // Generate recovery codes
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
    
    // Mark 2FA as passed since they just set it up
    session()->put('two_factor_passed', true);

    // Show recovery codes, then redirect to dashboard
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
            return redirect()->intended('/dashboard')->withCookie($cookie);
        }

        // Normal redirect (intended)
      
    return redirect()->route('dashboard');
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
