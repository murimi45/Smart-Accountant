<?php

use App\Http\Controllers\AccountantController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Platform\SchoolModuleController;
use App\Http\Controllers\TwoFactorController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('welcome'));
Route::get('/g', fn () => view('welcome'))->name('welcome');

Route::middleware('auth')->group(function () {
    Route::get('two-factor/setup', [TwoFactorController::class, 'showSetup'])->name('twofactor.setup');
    Route::post('two-factor/confirm', [TwoFactorController::class, 'confirmSetup'])->name('twofactor.confirm');
});

Route::middleware(['auth', '2fa'])->group(function () {
    Route::post('two-factor/disable', [TwoFactorController::class, 'disable'])->name('twofactor.disable');
});

Route::get('two-factor-challenge', [TwoFactorController::class, 'showChallenge'])
    ->name('twofactor.challenge');
Route::post('two-factor-challenge', [TwoFactorController::class, 'verifyChallenge'])
    ->name('twofactor.challenge.verify');

Route::middleware(['auth', 'school', 'tenant', '2fa'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'showDashboard'])->name('dashboard');

    Route::middleware('role:admin')->group(function () {
        Route::resource('admins', AdminController::class)->except(['show']);
        Route::resource('accountants', AccountantController::class)->except(['show']);
    });

    Route::post('/logout-and-login', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/login');
    })->name('logout.and.login');
});

Route::middleware(['auth', '2fa', 'role:platform'])
    ->prefix('platform')
    ->name('platform.')
    ->group(function () {
        Route::get('/school-modules', [SchoolModuleController::class, 'index'])
            ->name('school-modules.index');
        Route::post('/schools/{school}/modules/{slug}/enable', [SchoolModuleController::class, 'enable'])
            ->name('school-modules.enable');
        Route::post('/schools/{school}/modules/{slug}/disable', [SchoolModuleController::class, 'disable'])
            ->name('school-modules.disable');
    });
