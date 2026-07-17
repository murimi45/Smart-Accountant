<?php

namespace App\Modules\Academics\Providers;

use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\PromotionRun;
use App\Models\Stream;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\Term;
use App\Observers\EnrollmentObserver;
use App\Observers\StudentObserver;
use App\Policies\AcademicYearPolicy;
use App\Policies\ClassPolicy;
use App\Policies\PromotionRunPolicy;
use App\Policies\StreamPolicy;
use App\Policies\StudentEnrollmentPolicy;
use App\Policies\StudentPolicy;
use App\Policies\TermPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

/**
 * Always-on shared academics kernel.
 *
 * Academics is deliberately not protected by module entitlement middleware:
 * Finance, HR, and Grading all share the same students, classes, terms, and
 * enrollments.
 */
class AcademicsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../Routes/web.php');

        Gate::policy(Student::class, StudentPolicy::class);
        Gate::policy(StudentEnrollment::class, StudentEnrollmentPolicy::class);
        Gate::policy(PromotionRun::class, PromotionRunPolicy::class);
        Gate::policy(Stream::class, StreamPolicy::class);
        Gate::policy(Term::class, TermPolicy::class);
        Gate::policy(Classes::class, ClassPolicy::class);
        Gate::policy(AcademicYear::class, AcademicYearPolicy::class);

        Student::observe(StudentObserver::class);
        StudentEnrollment::observe(EnrollmentObserver::class);
    }
}
