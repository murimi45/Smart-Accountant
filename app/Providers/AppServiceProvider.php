<?php

namespace App\Providers;

use App\Core\Modules\ModuleRegistry;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        Blade::if('module', function (string $slug) {
            $schoolId = auth()->user()?->school_id;

            return $schoolId && ModuleRegistry::schoolHas((int) $schoolId, $slug);
        });

        Blade::if('role', function (string ...$roles) {
            $user = auth()->user();

            return $user && $user->hasRole(...$roles);
        });
    }
}
