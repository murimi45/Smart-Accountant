<?php

namespace App\Modules\Grading\Providers;

use App\Models\Assessment;
use App\Models\ClassSubject;
use App\Models\SchoolGradingSetting;
use App\Models\Subject;
use App\Models\TeacherSubjectAssignment;
use App\Models\TermReportCard;
use App\Policies\AssessmentPolicy;
use App\Policies\ClassSubjectPolicy;
use App\Policies\SchoolGradingSettingPolicy;
use App\Policies\SubjectPolicy;
use App\Policies\TeacherSubjectAssignmentPolicy;
use App\Policies\TermReportCardPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class GradingServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../Routes/web.php');
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'grading');

        Gate::policy(Subject::class, SubjectPolicy::class);
        Gate::policy(ClassSubject::class, ClassSubjectPolicy::class);
        Gate::policy(Assessment::class, AssessmentPolicy::class);
        Gate::policy(TermReportCard::class, TermReportCardPolicy::class);
        Gate::policy(TeacherSubjectAssignment::class, TeacherSubjectAssignmentPolicy::class);
        Gate::policy(SchoolGradingSetting::class, SchoolGradingSettingPolicy::class);
    }
}
