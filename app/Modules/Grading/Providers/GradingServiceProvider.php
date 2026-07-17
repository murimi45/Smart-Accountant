<?php

namespace App\Modules\Grading\Providers;

use Illuminate\Support\ServiceProvider;

class GradingServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../Routes/web.php');
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'grading');
    }
}
