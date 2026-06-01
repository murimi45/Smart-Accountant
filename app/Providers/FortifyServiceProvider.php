<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
   public function register(): void
{
    $this->app->singleton(LoginResponseContract::class, function () {
        return new class implements LoginResponseContract {
            public function toResponse($request)
            {
                $user = $request->user();

                \Log::info('LoginResponse triggered', [
                    'user' => $user?->id,
                    'two_factor_enabled' => $user?->two_factor_enabled,
                    'is_sensitive' => $user?->isSensitiveRole(),
                ]);

                // If user is sensitive role but hasn't set up 2FA yet, force setup
                if ($user && $user->isSensitiveRole() && !$user->two_factor_enabled) {
                    \Log::info('Redirecting to 2FA setup (first time)');
                    session()->put('must_setup_2fa', true);
                    return redirect()->route('twofactor.setup')
                        ->with('info', 'Two-factor authentication is required for your role. Please complete the setup to continue.');
                }

                // If user has 2FA enabled, challenge them
                if ($user && $user->isSensitiveRole() && $user->two_factor_enabled) {
                    \Log::info('Redirecting to 2FA challenge');
                    session()->forget('two_factor_passed');
                    return redirect()->route('twofactor.challenge');
                }

                // Default behaviour
                return redirect()->intended(config('fortify.home', '/dashboard'));
            }
        };
    });
}

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
         \Laravel\Fortify\Fortify::loginView(fn () => view('auth.login'));
    \Laravel\Fortify\Fortify::registerView(fn () => view('auth.register'));
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::redirectUserForTwoFactorAuthenticationUsing(RedirectIfTwoFactorAuthenticatable::class);

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }
}
