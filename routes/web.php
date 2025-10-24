<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\ClassFeeController;
use App\Http\Controllers\ExtraFeeController;
use App\Http\Controllers\TermController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\StatementController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\IncomeCategoryController;
use App\Http\Controllers\OtherIncomeController;
use App\Http\Controllers\CashbookController;
use App\Http\Controllers\PaymentChannelController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\SmsLogController;


// ✅ Public (No login)
Route::get('/register', [RegisterController::class, 'showRegistrationForm']);
Route::post('/register', [RegisterController::class, 'register'])->name('register');
Route::get('/login', [RegisterController::class, 'showLoginForm']);
Route::post('/login', [RegisterController::class, 'login'])->name('login');


// ✅ All logged-in tenants
Route::middleware(['school'])->group(function () {

    // ✅ SHARED (Admin + Accountant)
    Route::get('/dashboard', [DashboardController::class, 'showDashboard'])->name('dashboard');

    Route::get('/statements/{student}', [StatementController::class, 'single'])->name('statements.single');
    Route::post('/statements/bulk', [StatementController::class, 'bulk'])->name('statements.bulk');
    Route::post('/balances/sms/send', [StatementController::class, 'sendBulkBalanceSms'])->name('balances.sms.send');

    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/{invoice}/print', [InvoiceController::class, 'print'])->name('invoices.print');
    Route::post('/invoices/{invoice}/payments', [InvoiceController::class, 'storePayment'])->name('payments.store');

    Route::get('/sms/logs', [SmsLogController::class, 'index'])->name('sms.logs');


    // ✅ FINANCE (Admin + Accountant)
    Route::middleware(['role:admin,accountant'])->group(function () {

        Route::get('expense_categories', [ExpenseCategoryController::class,'index'])->name('expense_categories.index');
        Route::post('expense_categories', [ExpenseCategoryController::class,'store'])->name('expense_categories.store');
        Route::put('expense_categories/{id}', [ExpenseCategoryController::class,'update'])->name('expense_categories.update');
        Route::delete('expense_categories/{id}', [ExpenseCategoryController::class,'destroy'])->name('expense_categories.destroy');

        Route::get('expenses', [ExpenseController::class,'index'])->name('expenses.index');
        Route::get('expenses/create', [ExpenseController::class,'create'])->name('expenses.create');
        Route::post('expenses', [ExpenseController::class,'store'])->name('expenses.store');
        Route::get('expenses/{expense}/edit', [ExpenseController::class,'edit'])->name('expenses.edit');
        Route::put('expenses/{expense}', [ExpenseController::class,'update'])->name('expenses.update');
        Route::delete('expenses/{expense}', [ExpenseController::class,'destroy'])->name('expenses.destroy');
        Route::post('expenses/{id}/restore', [ExpenseController::class,'restore'])->name('expenses.restore');

        Route::get('income_categories', [IncomeCategoryController::class,'index'])->name('income_categories.index');
        Route::post('income_categories', [IncomeCategoryController::class,'store'])->name('income_categories.store');
        Route::put('income_categories/{id}', [IncomeCategoryController::class,'update'])->name('income_categories.update');
        Route::delete('income_categories/{id}', [IncomeCategoryController::class,'destroy'])->name('income_categories.destroy');

        Route::get('other_incomes', [OtherIncomeController::class, 'index'])->name('other_incomes.index');
        Route::get('other_incomes/create', [OtherIncomeController::class, 'create'])->name('other_incomes.create');
        Route::post('other_incomes', [OtherIncomeController::class, 'store'])->name('other_incomes.store');
        Route::get('other_incomes/{id}/edit', [OtherIncomeController::class, 'edit'])->name('other_incomes.edit');
        Route::put('other_incomes/{id}', [OtherIncomeController::class, 'update'])->name('other_incomes.update');
        Route::delete('other_incomes/{id}', [OtherIncomeController::class, 'destroy'])->name('other_incomes.destroy');

        Route::get('/cashbook', [CashbookController::class, 'index'])->name('cashbook.index');

        Route::get('/payment_channels', [PaymentChannelController::class, 'index'])->name('payment_channels.index');
        Route::post('/payment_channels', [PaymentChannelController::class, 'store'])->name('payment_channels.store');
        Route::put('/payment_channels/{id}', [PaymentChannelController::class, 'update'])->name('payment_channels.update');
        Route::get('/payment_channels/{id}/deactivate', [PaymentChannelController::class, 'deactivate'])->name('payment_channels.deactivate');
        Route::get('/payment_channels/{id}/activate', [PaymentChannelController::class, 'activate'])->name('payment_channels.activate');
    });


    // ✅ ADMIN ONLY (Academics + Promotions)
    Route::middleware(['role:admin'])->group(function () {

        Route::get('/student', [StudentController::class, 'listStudents'])->name('listStudents');
        Route::get('/addstudent', [StudentController::class, 'addStudents'])->name('addStudents');
        Route::post('/addstudent', [StudentController::class, 'insertStudents'])->name('insertStudents');
        Route::get('/editstudent/{id}', [StudentController::class, 'editStudents'])->name('editStudents');
        Route::post('/editstudent/{id}', [StudentController::class, 'updateStudents'])->name('updateStudent');
        Route::get('/deletestudent/{id}', [StudentController::class, 'deleteStudent'])->name('deleteStudent');
           

        Route::resource('admins', AdminController::class);       // for admins
        Route::resource('accountants', AccountantController::class);


        Route::get('/class', [ClassController::class, 'listClass'])->name('classlist');
        Route::post('/insertClass', [ClassController::class, 'insert'])->name('insertclass');
        Route::post('/editClass/{id}', [ClassController::class, 'update'])->name('editclass');
        Route::get('/deleteClass/{id}', [ClassController::class, 'delete'])->name('deleteclass');

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

        Route::post('/promotions/term', [PromotionController::class, 'promoteToNextTerm'])->name('promotions.term');
        Route::post('/promotions/class', [PromotionController::class, 'promoteToNextClass'])->name('promotions.class');
    });

});
