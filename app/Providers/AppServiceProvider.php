<?php

namespace App\Providers;
use App\Models\Student; 
use App\Models\Expense;
use App\Models\ClassFee;
use App\Observers\ClassFeeObserver;
use App\Models\StudentExtraFee; 
use App\Observers\ExtraFeeAssignmentObserver;
use App\Models\ExtraFee;
use App\Observers\ExtraFeeObserver;
use Illuminate\Support\ServiceProvider;
use App\Models\OtherIncome;
use App\Observers\OtherIncomeObserver;
use App\Models\Invoice;
use App\Observers\InvoiceObserver;
use App\Models\InvoicePayment;
use App\Models\InvoicePaymentReversal;
use App\Observers\InvoicePaymentObserver;
use App\Observers\InvoicePaymentReversalObserver;
use App\Observers\ExpenseObserver;
use App\Observers\StudentObserver;
use App\Models\StudentEnrollment;
use App\Observers\EnrollmentObserver;
use App\Models\CashbookEntry;
use App\Observers\CashbookEntryObserver;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Student::observe(StudentObserver::class);
        ClassFee::observe(ClassFeeObserver::class);
        ExtraFee::observe(ExtraFeeObserver::class);
        Invoice::observe(InvoiceObserver::class);
        StudentExtraFee::observe(ExtraFeeAssignmentObserver::class);
        Expense::observe(ExpenseObserver::class);
        OtherIncome::observe(OtherIncomeObserver::class);
        InvoicePayment::observe(InvoicePaymentObserver::class);
        InvoicePaymentReversal::observe(InvoicePaymentReversalObserver::class);
        StudentEnrollment::observe(EnrollmentObserver::class);
        CashbookEntry::observe(CashbookEntryObserver::class);
    }
}
