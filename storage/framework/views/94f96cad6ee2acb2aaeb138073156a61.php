<?php $__env->startSection('main'); ?>

<div class="main-wrapper">
    
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h4 class="mb-1"><?php echo e(isset($expense) ? 'Edit Expense' : 'Add Expense'); ?></h4>
                <p class="text-muted mb-0"><?php echo e(isset($expense) ? 'Update expense information' : 'Record a new expense transaction'); ?></p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <a href="<?php echo e(route('expenses.index')); ?>" class="btn btn-secondary">
                    <i class="fa fa-arrow-left me-2"></i>Back to List
                </a>
            </div>
        </div>
    </div>

    
    <?php if($errors->any()): ?>
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <div class="d-flex align-items-start">
                <i class="fa fa-exclamation-circle me-3 mt-1"></i>
                <div class="flex-grow-1">
                    <strong>Please fix the following errors:</strong>
                    <ul class="mb-0 mt-2">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    
    <div class="card form-card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fa fa-<?php echo e(isset($expense) ? 'edit' : 'plus-circle'); ?> me-2"></i>
                Expense Details
            </h5>
        </div>

        <div class="card-body">
            <form action="<?php echo e(isset($expense) ? route('expenses.update', $expense->id) : route('expenses.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php if(isset($expense)): ?>
                    <?php echo method_field('PUT'); ?>
                <?php endif; ?>

                
                <div class="form-section mb-4">
                    <h6 class="section-title mb-3">
                        <i class="fa fa-info-circle me-2"></i>Expense Information
                    </h6>

                    <div class="row">
                        
                        <div class="col-md-6 mb-3">
                            <label for="expense_category_id" class="form-label">
                                Category <span class="text-danger">*</span>
                            </label>
                            <select name="expense_category_id" 
                                    id="expense_category_id" 
                                    class="form-select <?php $__errorArgs = ['expense_category_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                    required>
                                <option value="">Select Category</option>
                                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($cat->id); ?>"
                                        <?php echo e((isset($expense) && $expense->expense_category_id == $cat->id) || old('expense_category_id') == $cat->id ? 'selected' : ''); ?>>
                                        <?php echo e($cat->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['expense_category_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        
                        <div class="col-md-6 mb-3">
                            <label for="amount" class="form-label">
                                Amount (KSh) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fa fa-money-bill-wave"></i>
                                </span>
                                <input type="number" 
                                       id="amount" 
                                       name="amount" 
                                       class="form-control <?php $__errorArgs = ['amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                       value="<?php echo e($expense->amount ?? old('amount')); ?>" 
                                       step="0.01"
                                       min="0"
                                       placeholder="Enter amount"
                                       required>
                                <?php $__errorArgs = ['amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>

                        
                        <div class="col-md-12 mb-3">
                            <label for="description" class="form-label">
                                Description
                            </label>
                            <textarea id="description" 
                                      name="description" 
                                      class="form-control <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                      rows="3"
                                      placeholder="Enter expense description"><?php echo e($expense->description ?? old('description')); ?></textarea>
                            <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                </div>

                
                <div class="form-section mb-4">
                    <h6 class="section-title mb-3">
                        <i class="fa fa-credit-card me-2"></i>Payment Details
                    </h6>

                    <div class="row">
                        
                        <div class="col-md-6 mb-3">
                            <label for="payment_method" class="form-label">
                                Payment Method <span class="text-danger">*</span>
                            </label>
                            <select name="payment_method" 
                                    id="payment_method" 
                                    class="form-select <?php $__errorArgs = ['payment_method'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                    required>
                                <option value="">Select Method</option>
                                <option value="cash" <?php echo e((isset($expense) && $expense->payment_method=='cash') || old('payment_method')=='cash' ? 'selected' : ''); ?>>Cash</option>
                                <option value="mpesa" <?php echo e((isset($expense) && $expense->payment_method=='mpesa') || old('payment_method')=='mpesa' ? 'selected' : ''); ?>>M-Pesa</option>
                                <option value="bank" <?php echo e((isset($expense) && $expense->payment_method=='bank') || old('payment_method')=='bank' ? 'selected' : ''); ?>>Bank Transfer</option>
                                <option value="cheque" <?php echo e((isset($expense) && $expense->payment_method=='cheque') || old('payment_method')=='cheque' ? 'selected' : ''); ?>>Cheque</option>
                            </select>
                            <?php $__errorArgs = ['payment_method'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        
                        <div class="col-md-6 mb-3">
                            <label for="expense_date" class="form-label">
                                Expense Date <span class="text-danger">*</span>
                            </label>
                            <input type="date" 
                                   id="expense_date" 
                                   name="expense_date" 
                                   class="form-control <?php $__errorArgs = ['expense_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   value="<?php echo e(optional($expense)->expense_date?->format('Y-m-d') ?? old('expense_date', date('Y-m-d'))); ?>"
                                   required>
                            <?php $__errorArgs = ['expense_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                </div>

                
                <div class="form-section mb-4">
                    <h6 class="section-title mb-3">
                        <i class="fa fa-calendar-alt me-2"></i>Academic Period
                    </h6>

                    <div class="row">
                        
                        <div class="col-md-12 mb-3">
                            <label for="term_id" class="form-label">
                                Term <span class="text-danger">*</span>
                            </label>
                            <select name="term_id" 
                                    id="term_id" 
                                    class="form-select <?php $__errorArgs = ['term_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                    required>
                                <option value="">Select Term</option>
                                <?php $__currentLoopData = $terms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $term): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($term->id); ?>"
                                        <?php echo e((isset($expense) && $expense->term_id == $term->id) || old('term_id') == $term->id ? 'selected' : ''); ?>>
                                        <?php echo e($term->name); ?> (<?php echo e($term->year); ?>)
                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['term_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                </div>

                
                <div class="form-actions pt-4 mt-4">
                    <div class="d-flex gap-2 justify-content-end">
                        <a href="<?php echo e(route('expenses.index')); ?>" class="btn btn-secondary">
                            <i class="fa fa-times me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-<?php echo e(isset($expense) ? 'primary' : 'success'); ?>">
                            <i class="fa fa-<?php echo e(isset($expense) ? 'check' : 'save'); ?> me-2"></i>
                            <?php echo e(isset($expense) ? 'Update Expense' : 'Save Expense'); ?>

                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Base Variables */
:root {
    --primary-color: #36a9e2;
    --success-color: #79c347;
    --success-dark: #5fa732;
    --danger-color: #ef4444;
    --gray-50: #f9fafb;
    --gray-100: #f3f4f6;
    --gray-200: #e5e7eb;
    --gray-300: #d1d5db;
    --gray-500: #6b7280;
    --gray-600: #4b5563;
    --gray-700: #374151;
    --gray-900: #111827;
    --border-radius: 8px;
}

/* Page Header */
.page-header h4 {
    font-size: 24px;
    font-weight: 600;
    color: var(--gray-900);
    margin: 0;
}

.page-header p {
    font-size: 14px;
    color: var(--gray-500);
}

/* Form Card */
.form-card {
    border: 1px solid var(--gray-200);
    border-radius: var(--border-radius);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.form-card .card-header {
    background: var(--gray-50);
    border-bottom: 1px solid var(--gray-200);
    padding: 16px 24px;
}

.form-card .card-header h5 {
    font-size: 18px;
    font-weight: 600;
    color: var(--gray-900);
    margin: 0;
}

.form-card .card-body {
    padding: 32px 24px;
}

/* Form Sections */
.form-section {
    margin-bottom: 0;
}

.section-title {
    font-size: 15px;
    font-weight: 600;
    color: var(--gray-700);
    padding-bottom: 12px;
    border-bottom: 1px solid var(--gray-200);
}

/* Form Elements */
.form-label {
    font-size: 13px;
    font-weight: 500;
    color: var(--gray-700);
    margin-bottom: 6px;
}

.form-control,
.form-select {
    border: 1px solid var(--gray-300);
    border-radius: var(--border-radius);
    padding: 8px 12px;
    font-size: 14px;
    transition: border-color 0.2s;
}

.form-control:focus,
.form-select:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(54, 169, 226, 0.1);
}

.form-control::placeholder {
    color: var(--gray-500);
    font-size: 14px;
}

/* Input Group */
.input-group-text {
    background-color: var(--gray-50);
    border: 1px solid var(--gray-300);
    border-right: none;
    color: var(--gray-600);
}

.input-group .form-control {
    border-left: none;
}

.input-group:focus-within .input-group-text {
    border-color: var(--primary-color);
    background-color: var(--gray-100);
}

.input-group:focus-within .form-control {
    border-color: var(--primary-color);
}

/* Textarea */
textarea.form-control {
    resize: vertical;
    min-height: 80px;
}

/* Validation States */
.is-invalid {
    border-color: var(--danger-color) !important;
}

.invalid-feedback {
    font-size: 13px;
    color: var(--danger-color);
    margin-top: 4px;
}

/* Buttons */
.btn {
    border-radius: var(--border-radius);
    padding: 8px 20px;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.2s;
}

.btn-primary {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
}

.btn-primary:hover {
    background-color: #2a8cbd;
    border-color: #2a8cbd;
}

.btn-success {
    background-color: var(--success-color);
    border-color: var(--success-color);
}

.btn-success:hover {
    background-color: var(--success-dark);
    border-color: var(--success-dark);
}

.btn-secondary {
    background-color: var(--gray-600);
    border-color: var(--gray-600);
}

.btn-secondary:hover {
    background-color: var(--gray-700);
    border-color: var(--gray-700);
}

/* Form Actions */
.form-actions {
    border-top: 1px solid var(--gray-200);
}

/* Alerts */
.alert {
    border-radius: var(--border-radius);
    border: none;
    padding: 16px;
}

.alert-danger {
    background-color: #fee2e2;
    color: #991b1b;
}

.alert ul {
    margin-bottom: 0;
    padding-left: 20px;
}

.alert li {
    margin-bottom: 4px;
}

.alert li:last-child {
    margin-bottom: 0;
}

/* Date Input */
input[type="date"]::-webkit-calendar-picker-indicator {
    cursor: pointer;
    opacity: 0.6;
}

input[type="date"]::-webkit-calendar-picker-indicator:hover {
    opacity: 1;
}

/* Responsive Design */
@media (max-width: 768px) {
    .form-card .card-body {
        padding: 24px 16px;
    }

    .page-header h4 {
        font-size: 20px;
    }

    .form-actions .d-flex {
        flex-direction: column-reverse;
    }

    .form-actions .btn {
        width: 100%;
        margin-bottom: 8px;
    }

    .form-actions .btn:last-child {
        margin-bottom: 0;
    }
}

@media (max-width: 576px) {
    .section-title {
        font-size: 14px;
    }

    .form-card .card-header h5 {
        font-size: 16px;
    }
}
</style>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Allan\smart_accountant2\resources\views/expensesdefined/create.blade.php ENDPATH**/ ?>