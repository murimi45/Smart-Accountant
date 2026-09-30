<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AgedDebtorsController;
use App\Http\Controllers\BankReconciliationController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\BulkImportExportController;
use App\Http\Controllers\CashbookController;
use App\Http\Controllers\ClassFeeController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ExtraFeeController;
use App\Http\Controllers\FeeReminderController;
use App\Http\Controllers\FinanceAuditLogController;
use App\Http\Controllers\FinancialReportsController;
use App\Http\Controllers\IncomeCategoryController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\InvoiceWaiverController;
use App\Http\Controllers\LedgerController;
use App\Http\Controllers\OtherIncomeController;
use App\Http\Controllers\PaymentChannelController;
use App\Http\Controllers\SmsLogController;
use App\Http\Controllers\StatementController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\SalaryGradeController;
use App\Http\Controllers\EmployeeDeductionController;
use App\Http\Controllers\PayrollRunController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'school', 'tenant', '2fa', 'module:accountant'])
    ->group(function () {
        Route::get('/statements/{student}', [StatementController::class, 'single'])->name('statements.single');
        Route::post('/statements/bulk', [StatementController::class, 'bulk'])->name('statements.bulk');
        Route::post('/statements/bulk/balance', [StatementController::class, 'bulkBalanceStatements'])
            ->name('balances.statements.bulk');
        Route::post('/balances/sms/send', [StatementController::class, 'sendBulkBalanceSms'])
            ->name('balances.sms.send');

        Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
        Route::post('/invoices/{invoice}/payments', [InvoiceController::class, 'storePayment'])->name('payments.store');
        Route::post('/invoices/{invoice}/payments/{payment}/reverse', [InvoiceController::class, 'reversePayment'])
            ->name('payments.reverse');

        Route::get('/waivers', [InvoiceWaiverController::class, 'index'])->name('waivers.index');
        Route::post('/invoices/{invoice}/waivers', [InvoiceWaiverController::class, 'store'])->name('waivers.store');
        Route::post('/waivers/{waiver}/approve', [InvoiceWaiverController::class, 'approve'])->name('waivers.approve');
        Route::post('/waivers/{waiver}/reject', [InvoiceWaiverController::class, 'reject'])->name('waivers.reject');

        Route::get('/reports/aged-debtors', [AgedDebtorsController::class, 'index'])->name('reports.aged-debtors');
        Route::get('/reports/aged-debtors/export/pdf', [AgedDebtorsController::class, 'exportPdf'])
            ->name('reports.aged-debtors.pdf');
        Route::get('/reports/aged-debtors/export/excel', [AgedDebtorsController::class, 'exportExcel'])
            ->name('reports.aged-debtors.excel');
        Route::get('/audit-log', [FinanceAuditLogController::class, 'index'])->name('audit.index');

        Route::get('/sms/logs', [SmsLogController::class, 'index'])->name('sms.logs');
        Route::get('/sms/reminders', [FeeReminderController::class, 'edit'])->name('reminders.edit');
        Route::put('/sms/reminders', [FeeReminderController::class, 'update'])->name('reminders.update');
        Route::post('/sms/reminders/run', [FeeReminderController::class, 'runNow'])->name('reminders.run');

        Route::middleware('role:admin,accountant')->group(function () {
            Route::get('expense_categories', [ExpenseCategoryController::class, 'index'])->name('expense_categories.index');
            Route::post('expense_categories', [ExpenseCategoryController::class, 'store'])->name('expense_categories.store');
            Route::put('expense_categories/{id}', [ExpenseCategoryController::class, 'update'])->name('expense_categories.update');
            Route::delete('expense_categories/{id}', [ExpenseCategoryController::class, 'destroy'])->name('expense_categories.destroy');

            Route::get('expenses', [ExpenseController::class, 'index'])->name('expenses.index');
            Route::get('expenses/create', [ExpenseController::class, 'create'])->name('expenses.create');
            Route::post('expenses', [ExpenseController::class, 'store'])->name('expenses.store');
            Route::get('expenses/{expense}/edit', [ExpenseController::class, 'edit'])->name('expenses.edit');
            Route::put('expenses/{expense}', [ExpenseController::class, 'update'])->name('expenses.update');
            Route::delete('expenses/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');
            Route::post('expenses/{id}/restore', [ExpenseController::class, 'restore'])->name('expenses.restore');

            Route::get('budgets', [BudgetController::class, 'index'])->name('budgets.index');
            Route::put('budgets', [BudgetController::class, 'update'])->name('budgets.update');
            Route::get('reports/budget-variance', [BudgetController::class, 'variance'])->name('reports.budget-variance');

            Route::get('income_categories', [IncomeCategoryController::class, 'index'])->name('income_categories.index');
            Route::post('income_categories', [IncomeCategoryController::class, 'store'])->name('income_categories.store');
            Route::put('income_categories/{id}', [IncomeCategoryController::class, 'update'])->name('income_categories.update');
            Route::delete('income_categories/{id}', [IncomeCategoryController::class, 'destroy'])->name('income_categories.destroy');

            Route::get('other_incomes', [OtherIncomeController::class, 'index'])->name('other_incomes.index');
            Route::get('other_incomes/create', [OtherIncomeController::class, 'create'])->name('other_incomes.create');
            Route::post('other_incomes', [OtherIncomeController::class, 'store'])->name('other_incomes.store');
            Route::get('other_incomes/{id}/edit', [OtherIncomeController::class, 'edit'])->name('other_incomes.edit');
            Route::put('other_incomes/{id}', [OtherIncomeController::class, 'update'])->name('other_incomes.update');
            Route::delete('other_incomes/{id}', [OtherIncomeController::class, 'destroy'])->name('other_incomes.destroy');

            Route::get('/classfee', [ClassFeeController::class, 'listClassFee'])->name('classfeelist');
            Route::get('/addclassfee', [ClassFeeController::class, 'addclassfee'])->name('addclassfee');
            Route::post('/addclassfee', [ClassFeeController::class, 'insertclassfee'])->name('insertclassfee');
            Route::get('/editclassfee/{id}', [ClassFeeController::class, 'editclassfee'])->name('editclassfee');
            Route::post('/editclassfee/{id}', [ClassFeeController::class, 'updateclassfee'])->name('updateclassfee');
            Route::get('/deleteclassfee/{id}', [ClassFeeController::class, 'deleteclassfee'])->name('deleteclassfee');

            Route::get('/extrafee', [ExtraFeeController::class, 'listExtraFee'])->name('extrafeelist');
            Route::get('/addextrafee', [ExtraFeeController::class, 'addExtraFee'])->name('addextrafee');
            Route::post('/addextrafee', [ExtraFeeController::class, 'insertExtraFee'])->name('insertextrafee');
            Route::post('/editextrafee/{id}', [ExtraFeeController::class, 'editExtraFee'])->name('editextrafee');
            Route::get('/editextrafee/{id}', [ExtraFeeController::class, 'updateExtraFee'])->name('updateextrafee');
            Route::get('/deleteextrafee/{id}', [ExtraFeeController::class, 'deleteExtraFee'])->name('deleteextrafee');

            Route::get('/assignextrafee', [ExtraFeeController::class, 'showAssignExtraFeeForm'])->name('assignextrafeeform');
            Route::post('/assignextrafee', [ExtraFeeController::class, 'assignStudentExtraFee'])->name('assignextrafee');
            Route::get('/listextrafeestudents', [ExtraFeeController::class, 'listExtraFeeStudent'])->name('listextrafeestudents');
            Route::get('/assign-extra-fee/edit/{id}', [ExtraFeeController::class, 'editAssignedExtraFee'])
                ->name('editassignedextrafee');
            Route::post('/assign-extra-fee/edit/{id}', [ExtraFeeController::class, 'updateAssignedExtraFee'])
                ->name('updateassignedextrafee');
            Route::get('/assign-extra-fee/delete/{id}', [ExtraFeeController::class, 'deleteAssignedExtraFee'])
                ->name('deleteassignedextrafee');

            Route::get('/cashbook', [CashbookController::class, 'index'])->name('cashbook.index');
            Route::get('/accounts', [AccountController::class, 'index'])->name('accounts.index');
            Route::get('/ledger', [LedgerController::class, 'index'])->name('ledger.index');
            Route::get('/reports/financial', [FinancialReportsController::class, 'index'])->name('reports.financial');
            Route::get('/reports/financial/export/pdf', [FinancialReportsController::class, 'exportPdf'])
                ->name('reports.financial.pdf');

            Route::get('/reconciliation', [BankReconciliationController::class, 'index'])->name('reconciliation.index');
            Route::post('/reconciliation/deposits', [BankReconciliationController::class, 'storeDeposit'])
                ->name('reconciliation.deposits.store');
            Route::put('/reconciliation/deposits/{deposit}', [BankReconciliationController::class, 'updateDeposit'])
                ->name('reconciliation.deposits.update');
            Route::delete('/reconciliation/deposits/{deposit}', [BankReconciliationController::class, 'destroyDeposit'])
                ->name('reconciliation.deposits.destroy');
            Route::post('/reconciliation/match', [BankReconciliationController::class, 'match'])
                ->name('reconciliation.match');
            Route::delete('/reconciliation/matches/{match}', [BankReconciliationController::class, 'unmatch'])
                ->name('reconciliation.unmatch');

            Route::get('/bulk', [BulkImportExportController::class, 'index'])->name('bulk.index');
            Route::get('/bulk/export/{type}', [BulkImportExportController::class, 'export'])->name('bulk.export');
            Route::get('/bulk/template/{type}', [BulkImportExportController::class, 'template'])->name('bulk.template');
            Route::post('/bulk/import/{type}', [BulkImportExportController::class, 'import'])->name('bulk.import');
 
            
            Route::get('/payroll/runs', [PayrollRunController::class, 'index'])->name('payroll.runs.index');
            Route::post('/payroll/runs', [PayrollRunController::class, 'store'])->name('payroll.runs.store');
            Route::get('/payroll/runs/{id}', [PayrollRunController::class, 'show'])->name('payroll.runs.show');
            Route::post('/payroll/runs/{id}/recalculate', [PayrollRunController::class, 'recalculate'])->name('payroll.runs.recalculate');
            Route::post('/payroll/runs/{id}/lock', [PayrollRunController::class, 'lock'])->name('payroll.runs.lock');
            Route::put('/payroll/runs/{runId}/payslips/{payslipId}', [PayrollRunController::class, 'updatePayslip'])->name('payroll.payslips.update');

            Route::get('/payroll/grades', [SalaryGradeController::class, 'index'])->name('salary_grades.index');
            Route::post('/payroll/grades', [SalaryGradeController::class, 'store'])->name('salary_grades.store');
            Route::put('/payroll/grades/{id}', [SalaryGradeController::class, 'update'])->name('salary_grades.update');
            Route::delete('/payroll/grades/{id}', [SalaryGradeController::class, 'destroy'])->name('salary_grades.destroy');

            Route::get('/payroll/employees', [EmployeeController::class, 'index'])->name('employees.index');
            Route::get('/payroll/employees/create', [EmployeeController::class, 'create'])->name('employees.create');
            Route::post('/payroll/employees', [EmployeeController::class, 'store'])->name('employees.store');
            Route::get('/payroll/employees/{id}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
           
           
           
           Route::post('/payroll/employees/{employeeId}/deductions', [EmployeeDeductionController::class, 'store'])
                 ->name('employees.deductions.store');
           Route::put('/payroll/employees/{employeeId}/deductions/{deductionId}', [EmployeeDeductionController::class, 'update'])
                 ->name('employees.deductions.update');
            Route::post('/payroll/employees/{employeeId}/deductions/{deductionId}/stop', [EmployeeDeductionController::class, 'stop'])
                 ->name('employees.deductions.stop');
           
           
            Route::put('/payroll/employees/{id}', [EmployeeController::class, 'update'])->name('employees.update');
            Route::get('/payment_channels', [PaymentChannelController::class, 'index'])->name('payment_channels.index');
            Route::post('/payment_channels', [PaymentChannelController::class, 'store'])->name('payment_channels.store');
            Route::put('/payment_channels/{id}', [PaymentChannelController::class, 'update'])->name('payment_channels.update');
            Route::get('/payment_channels/{id}/deactivate', [PaymentChannelController::class, 'deactivate'])
                ->name('payment_channels.deactivate');
            Route::get('/payment_channels/{id}/activate', [PaymentChannelController::class, 'activate'])
                ->name('payment_channels.activate');
        });
    });
