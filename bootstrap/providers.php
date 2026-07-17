<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\AuthServiceProvider::class,
    App\Providers\EventServiceProvider::class,
    App\Modules\Academics\Providers\AcademicsServiceProvider::class,
    App\Modules\Finance\Providers\FinanceServiceProvider::class,
    App\Modules\Hr\Providers\HrServiceProvider::class,
    App\Modules\Grading\Providers\GradingServiceProvider::class,
];
