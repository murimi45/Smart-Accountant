<?php

use App\Http\Controllers\Grading\AssessmentController;
use App\Http\Controllers\Grading\AssessmentTypeController;
use App\Http\Controllers\Grading\AssessmentWeightController;
use App\Http\Controllers\Grading\ClassSubjectController;
use App\Http\Controllers\Grading\GradingDashboardController;
use App\Http\Controllers\Grading\GradingSettingsController;
use App\Http\Controllers\Grading\MarkEntryController;
use App\Http\Controllers\Grading\ReportCardController;
use App\Http\Controllers\Grading\SubjectController;
use App\Http\Controllers\Grading\TeacherAssignmentController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'school', 'tenant', '2fa', 'module:grading', 'role:admin,teacher'])
    ->prefix('grading')
    ->name('grading.')
    ->group(function () {
        Route::get('/', [GradingDashboardController::class, 'index'])->name('index');

        Route::get('mark-entry', [MarkEntryController::class, 'index'])->name('mark-entry.index');
        Route::get('mark-entry/{assessment}', [MarkEntryController::class, 'show'])->name('mark-entry.show');

        Route::get('assessments', [AssessmentController::class, 'index'])->name('assessments.index');
        Route::get('assessments/create', [AssessmentController::class, 'create'])->name('assessments.create');
        Route::post('assessments', [AssessmentController::class, 'store'])->name('assessments.store');
        Route::get('assessments/{assessment}/edit', [AssessmentController::class, 'edit'])->name('assessments.edit');
        Route::put('assessments/{assessment}', [AssessmentController::class, 'update'])->name('assessments.update');
        Route::delete('assessments/{assessment}', [AssessmentController::class, 'destroy'])->name('assessments.destroy');

        Route::get('report-cards', [ReportCardController::class, 'index'])->name('report-cards.index');
        Route::post('report-cards', [ReportCardController::class, 'store'])->name('report-cards.store');
        Route::post('report-cards/{reportCard}/publish', [ReportCardController::class, 'publish'])->name('report-cards.publish');
        Route::post('report-cards/{reportCard}/unpublish', [ReportCardController::class, 'unpublish'])->name('report-cards.unpublish');
        Route::get('report-cards/{reportCard}/download', [ReportCardController::class, 'download'])->name('report-cards.download');

        Route::middleware('role:admin')->group(function () {
            Route::get('settings', [GradingSettingsController::class, 'index'])->name('settings.index');
            Route::post('settings', [GradingSettingsController::class, 'store'])->name('settings.store');

            Route::resource('subjects', SubjectController::class)->except(['show']);

            Route::get('class-subjects', [ClassSubjectController::class, 'index'])->name('class-subjects.index');
            Route::get('class-subjects/create', [ClassSubjectController::class, 'create'])->name('class-subjects.create');
            Route::post('class-subjects', [ClassSubjectController::class, 'store'])->name('class-subjects.store');
            Route::delete('class-subjects/{classSubject}', [ClassSubjectController::class, 'destroy'])->name('class-subjects.destroy');

            Route::get('assessment-types', [AssessmentTypeController::class, 'index'])->name('assessment-types.index');
            Route::post('assessment-types', [AssessmentTypeController::class, 'store'])->name('assessment-types.store');
            Route::delete('assessment-types/{assessmentType}', [AssessmentTypeController::class, 'destroy'])->name('assessment-types.destroy');

            Route::get('weights', [AssessmentWeightController::class, 'index'])->name('weights.index');
            Route::post('weights', [AssessmentWeightController::class, 'store'])->name('weights.store');
            Route::delete('weights/{weight}', [AssessmentWeightController::class, 'destroy'])->name('weights.destroy');

            Route::get('assignments', [TeacherAssignmentController::class, 'index'])->name('assignments.index');
            Route::post('assignments', [TeacherAssignmentController::class, 'store'])->name('assignments.store');
            Route::delete('assignments/{assignment}', [TeacherAssignmentController::class, 'destroy'])->name('assignments.destroy');
        });
    });
