<?php

namespace App\Modules\Finance\Providers;

use App\Models\Account;
use App\Models\BankDeposit;
use App\Models\BankReconciliationMatch;
use App\Models\Budget;
use App\Models\CashbookEntry;
use App\Models\ClassFee;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\ExtraFee;
use App\Models\IncomeCategory;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\InvoicePaymentReversal;
use App\Models\InvoiceWaiver;
use App\Models\LedgerEntry;
use App\Models\OtherIncome;
use App\Models\PaymentChannel;
use App\Models\SmsLog;
use App\Models\StudentExtraFee;
use App\Models\Transaction;
use App\Observers\CashbookEntryObserver;
use App\Observers\ClassFeeObserver;
use App\Observers\ExpenseObserver;
use App\Observers\ExtraFeeAssignmentObserver;
use App\Observers\ExtraFeeObserver;
use App\Observers\InvoiceObserver;
use App\Observers\InvoicePaymentObserver;
use App\Observers\InvoicePaymentReversalObserver;
use App\Observers\OtherIncomeObserver;
use App\Policies\AccountPolicy;
use App\Policies\BankDepositPolicy;
use App\Policies\BankReconciliationMatchPolicy;
use App\Policies\BudgetPolicy;
use App\Policies\CashbookEntryPolicy;
use App\Policies\ClassFeePolicy;
use App\Policies\ExpenseCategoryPolicy;
use App\Policies\ExpensePolicy;
use App\Policies\ExtraFeePolicy;
use App\Policies\IncomeCategoryPolicy;
use App\Policies\InvoicePolicy;
use App\Policies\InvoiceWaiverPolicy;
use App\Policies\LedgerEntryPolicy;
use App\Policies\OtherIncomePolicy;
use App\Policies\PaymentChannelPolicy;
use App\Policies\SmsLogPolicy;
use App\Policies\StudentExtraFeePolicy;
use App\Policies\TransactionPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use App\Models\Employee;
use App\Models\SalaryGrade;
use App\Policies\EmployeePolicy;
use App\Policies\SalaryGradePolicy;
use App\Models\EmployeeDeduction;
use App\Models\PayrollRun;
use App\Policies\EmployeeDeductionPolicy;
use App\Policies\PayrollRunPolicy;

class FinanceServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../Routes/web.php');

        Gate::policy(Account::class, AccountPolicy::class);
        Gate::policy(Invoice::class, InvoicePolicy::class);
        Gate::policy(InvoiceWaiver::class, InvoiceWaiverPolicy::class);
        Gate::policy(StudentExtraFee::class, StudentExtraFeePolicy::class);
        Gate::policy(Expense::class, ExpensePolicy::class);
        Gate::policy(OtherIncome::class, OtherIncomePolicy::class);
        Gate::policy(ClassFee::class, ClassFeePolicy::class);
        Gate::policy(ExtraFee::class, ExtraFeePolicy::class);
        Gate::policy(ExpenseCategory::class, ExpenseCategoryPolicy::class);
        Gate::policy(Budget::class, BudgetPolicy::class);
        Gate::policy(IncomeCategory::class, IncomeCategoryPolicy::class);
        Gate::policy(PaymentChannel::class, PaymentChannelPolicy::class);
        Gate::policy(CashbookEntry::class, CashbookEntryPolicy::class);
        Gate::policy(LedgerEntry::class, LedgerEntryPolicy::class);
        Gate::policy(BankDeposit::class, BankDepositPolicy::class);
        Gate::policy(BankReconciliationMatch::class, BankReconciliationMatchPolicy::class);
        Gate::policy(SmsLog::class, SmsLogPolicy::class);
        Gate::policy(Transaction::class, TransactionPolicy::class);
        Gate::policy(SalaryGrade::class, SalaryGradePolicy::class);
        Gate::policy(Employee::class, EmployeePolicy::class);
        Gate::policy(EmployeeDeduction::class, EmployeeDeductionPolicy::class);
        Gate::policy(PayrollRun::class, PayrollRunPolicy::class);

        ClassFee::observe(ClassFeeObserver::class);
        ExtraFee::observe(ExtraFeeObserver::class);
        Invoice::observe(InvoiceObserver::class);
        StudentExtraFee::observe(ExtraFeeAssignmentObserver::class);
        Expense::observe(ExpenseObserver::class);
        OtherIncome::observe(OtherIncomeObserver::class);
        InvoicePayment::observe(InvoicePaymentObserver::class);
        InvoicePaymentReversal::observe(InvoicePaymentReversalObserver::class);
        CashbookEntry::observe(CashbookEntryObserver::class);
    }
}
