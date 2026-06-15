<?php

namespace App\Providers;

use App\Models\Classes;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\OtherIncome;
use App\Models\PromotionRun;
use App\Models\Stream;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\StudentExtraFee;
use App\Models\Term;
use App\Models\User;
use App\Policies\ClassPolicy;
use App\Policies\ExpensePolicy;
use App\Policies\InvoicePolicy;
use App\Policies\OtherIncomePolicy;
use App\Policies\PromotionRunPolicy;
use App\Policies\StreamPolicy;
use App\Policies\StudentEnrollmentPolicy;
use App\Policies\StudentExtraFeePolicy;
use App\Policies\StudentPolicy;
use App\Policies\TermPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Student::class           => StudentPolicy::class,
        StudentEnrollment::class => StudentEnrollmentPolicy::class,
        Invoice::class           => InvoicePolicy::class,
        PromotionRun::class      => PromotionRunPolicy::class,
        StudentExtraFee::class   => StudentExtraFeePolicy::class,
        User::class              => UserPolicy::class,
        Stream::class            => StreamPolicy::class,
        Expense::class           => ExpensePolicy::class,
        OtherIncome::class       => OtherIncomePolicy::class,
        Term::class              => TermPolicy::class,
        Classes::class           => ClassPolicy::class,
    ];

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        foreach ($this->policies as $model => $policy) {
            Gate::policy($model, $policy);
        }
    }
}
