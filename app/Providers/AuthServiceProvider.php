<?php

namespace App\Providers;

use App\Models\AcademicYear;
use App\Models\Budget;
use App\Models\Classes;
use App\Models\ClassFee;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\ExtraFee;
use App\Models\IncomeCategory;
use App\Models\Invoice;
use App\Models\InvoiceWaiver;
use App\Models\OtherIncome;
use App\Models\PaymentChannel;
use App\Models\PromotionRun;
use App\Models\Stream;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\StudentExtraFee;
use App\Models\Term;
use App\Models\User;
use App\Policies\AcademicYearPolicy;
use App\Policies\BudgetPolicy;
use App\Policies\ClassFeePolicy;
use App\Policies\ClassPolicy;
use App\Policies\ExpenseCategoryPolicy;
use App\Policies\ExpensePolicy;
use App\Policies\ExtraFeePolicy;
use App\Policies\IncomeCategoryPolicy;
use App\Policies\InvoicePolicy;
use App\Policies\InvoiceWaiverPolicy;
use App\Policies\OtherIncomePolicy;
use App\Policies\PaymentChannelPolicy;
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
        InvoiceWaiver::class     => InvoiceWaiverPolicy::class,
        PromotionRun::class      => PromotionRunPolicy::class,
        StudentExtraFee::class   => StudentExtraFeePolicy::class,
        User::class              => UserPolicy::class,
        Stream::class            => StreamPolicy::class,
        Expense::class           => ExpensePolicy::class,
        OtherIncome::class       => OtherIncomePolicy::class,
        Term::class              => TermPolicy::class,
        Classes::class           => ClassPolicy::class,
        AcademicYear::class      => AcademicYearPolicy::class,
        ClassFee::class          => ClassFeePolicy::class,
        ExtraFee::class          => ExtraFeePolicy::class,
        ExpenseCategory::class   => ExpenseCategoryPolicy::class,
        Budget::class            => BudgetPolicy::class,
        IncomeCategory::class    => IncomeCategoryPolicy::class,
        PaymentChannel::class    => PaymentChannelPolicy::class,
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
