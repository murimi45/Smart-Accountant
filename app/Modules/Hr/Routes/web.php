<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'school', 'tenant', '2fa', 'module:hr', 'role:admin,hr_manager'])
    ->prefix('hr')
    ->name('hr.')
    ->group(function () {
        Route::get('/', fn () => view('hr::coming-soon'))->name('index');
    });
