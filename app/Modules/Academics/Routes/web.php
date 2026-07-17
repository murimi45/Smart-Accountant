<?php

use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\PromotionProgressController;
use App\Http\Controllers\StreamController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TermController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'school', 'tenant', '2fa'])
    ->group(function () {
        Route::middleware('role:admin,accountant')->group(function () {
            Route::resource('academic-years', AcademicYearController::class)
                ->only(['index', 'store', 'update', 'destroy']);

            Route::get('/term', [TermController::class, 'listTerm'])->name('termlist');
            Route::get('/addterm', [TermController::class, 'addTerm'])->name('addterm');
            Route::post('/addterm', [TermController::class, 'insertTerm'])->name('insertterm');
            Route::post('/editterm/{id}', [TermController::class, 'editterm'])->name('editterm');
            Route::get('/editterm/{id}', [TermController::class, 'updateterm'])->name('updateterm');
            Route::get('/deleteterm/{id}', [TermController::class, 'delete'])->name('deleteterm');

            Route::resource('streams', StreamController::class)->only(['index', 'store', 'update', 'destroy']);

            Route::get('/class', [ClassController::class, 'listClass'])->name('classlist');
            Route::post('/insertClass', [ClassController::class, 'insert'])->name('insertclass');
            Route::post('/editClass/{id}', [ClassController::class, 'update'])->name('editclass');
            Route::get('/deleteClass/{id}', [ClassController::class, 'delete'])->name('deleteclass');
        });

        Route::middleware('role:admin')->group(function () {
            Route::get('/student', [StudentController::class, 'listStudents'])->name('listStudents');
            Route::get('/addstudent', [StudentController::class, 'addStudents'])->name('addStudents');
            Route::post('/addstudent', [StudentController::class, 'insertStudents'])->name('insertStudents');
            Route::get('/editstudent/{id}', [StudentController::class, 'editStudents'])->name('editStudents');
            Route::post('/editstudent/{id}', [StudentController::class, 'updateStudents'])->name('updateStudent');
            Route::get('/deletestudent/{id}', [StudentController::class, 'deleteStudent'])->name('deleteStudent');

            Route::post('/promotion/term', [PromotionController::class, 'promoteToNextTerm'])
                ->name('promotion.term');
            Route::post('/promotion/class', [PromotionController::class, 'promoteToNextClass'])
                ->name('promotion.class');
            Route::get('/promotion/{promotionRun}/progress', [PromotionProgressController::class, 'show'])
                ->name('promotion.progress');
            Route::get('/promotion/{promotionRun}/poll', [PromotionProgressController::class, 'poll'])
                ->name('promotion.poll');

            Route::prefix('enrollment')->name('enrollment.')->group(function () {
                Route::get('/', [EnrollmentController::class, 'index'])->name('index');
                Route::post('/{enrollment}/status', [EnrollmentController::class, 'updateStatus'])
                    ->name('update-status');
                Route::get('/{enrollment}/correction', [EnrollmentController::class, 'showCorrection'])
                    ->name('show-correction');
                Route::post('/{enrollment}/correction', [EnrollmentController::class, 'executeCorrection'])
                    ->name('execute-correction');
                Route::post('/bulk-status', [EnrollmentController::class, 'bulkUpdateStatus'])
                    ->name('bulk-status');
            });
        });
    });
