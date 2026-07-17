<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'school', 'tenant', '2fa', 'module:grading', 'role:admin,teacher'])
    ->prefix('grading')
    ->name('grading.')
    ->group(function () {
        Route::get('/', fn () => view('grading::coming-soon'))->name('index');
    });
