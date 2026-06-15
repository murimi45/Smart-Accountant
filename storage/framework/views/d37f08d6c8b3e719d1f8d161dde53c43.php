

<?php $__env->startSection('main'); ?>
<div class="container-fluid px-4">
    
    <div class="page_title">
        <div class="row align-items-center mb-4">
            <div class="col-md-6">
                <h4 class="mb-0">Add Other Income</h4>
                <p class="text-muted mb-0 mt-1">Record additional income transactions</p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <a href="<?php echo e(route('other_incomes.index')); ?>" class="btn btn-secondary px-4 py-2">
                    <i class="fa fa-arrow-left me-2"></i>Back to List
                </a>
            </div>
        </div>
    </div>

    
    <?php if($errors->any()): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert" style="border-left: 4px solid #dc3545; border-radius: 8px;">
            <div class="d-flex align-items-start">
                <i class="fa fa-exclamation-circle me-3 mt-1" style="font-size: 20px;"></i>
                <div class="flex-grow-1">
                    <strong>Please fix the following errors:</strong>
                    <ul class="mb-0 mt-2 ps-3">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    
    <div class="row">
        <div class="col-12">
            <div class="white_shd full margin_bottom_30" style="border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); border: 1px solid #f0f0f0;">
                <div class="full graph_head" style="background: linear-gradient(135deg, #79c347 0%, #5fa732 100%); padding: 25px 30px; border-radius: 12px 12px 0 0;">
                    <div class="heading1 margin_0">
                        <h2 style="font-size: 20px; color: #fff; font-weight: 600; margin: 0; display: flex; align-items: center;">
                            <i class="fa fa-wallet me-3" style="font-size: 24px;"></i>
                            Income Details
                        </h2>
                        <p class="mb-0 mt-2" style="color: rgba(255,255,255,0.9); font-size: 14px;">Fill in the details below to record other income</p>
                    </div>
                </div>

                <div class="full inner_elements" style="padding: 35px 30px;">
                    <form action="<?php echo e(route('other_incomes.store')); ?>" method="POST">
                        <?php echo csrf_field(); ?>

                        
                        <div class="form-section mb-4">
                            <h5 class="section-title mb-4" style="color: #2c3e50; font-weight: 600; font-size: 16px; display: flex; align-items: center; padding-bottom: 12px; border-bottom: 2px solid #e8eaed;">
                                <i class="fa fa-info-circle me-2" style="color: #79c347;"></i>
                                Income Information
                            </h5>

                            <div class="row">
                                
                                <div class="col-md-6 col-lg-3 mb-4">
                                    <label for="income_category_id" class="form-label fw-semibold" style="font-size: 14px; color: #495057; margin-bottom: 10px;">
                                        <i class="fa fa-tags me-2 text-primary"></i>Income Category <span class="text-danger">*</span>
                                    </label>
                                    <select name="income_category_id" 
                                            id="income_category_id"
                                            class="form-select <?php $__errorArgs = ['income_category_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                            style="border-radius: 8px; border: 1px solid #e0e0e0; padding: 12px 16px; font-size: 14px;"
                                            required>
                                        <option value="">-- Select Category --</option>
                                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($cat->id); ?>" <?php echo e(old('income_category_id') == $cat->id ? 'selected' : ''); ?>>
                                                <?php echo e($cat->name); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <?php $__errorArgs = ['income_category_id'];
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

                                
                                <div class="col-md-6 col-lg-3 mb-4">
                                    <label for="amount" class="form-label fw-semibold" style="font-size: 14px; color: #495057; margin-bottom: 10px;">
                                        <i class="fa fa-coins me-2 text-success"></i>Amount (KSh) <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group" style="border-radius: 8px;">
                                        <span class="input-group-text" style="background: #f8f9fa; border: 1px solid #e0e0e0; border-radius: 8px 0 0 8px;">
                                            <i class="fa fa-money-bill-wave text-success"></i>
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
                                               step="0.01" 
                                               min="0"
                                               placeholder="Enter amount"
                                               value="<?php echo e(old('amount')); ?>"
                                               style="border-radius: 0 8px 8px 0; border: 1px solid #e0e0e0; padding: 12px 16px; font-size: 14px;"
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

                                
                                <div class="col-md-6 col-lg-3 mb-4">
                                    <label for="payment_method" class="form-label fw-semibold" style="font-size: 14px; color: #495057; margin-bottom: 10px;">
                                        <i class="fa fa-credit-card me-2 text-info"></i>Payment Method
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
                                            style="border-radius: 8px; border: 1px solid #e0e0e0; padding: 12px 16px; font-size: 14px;">
                                        <option value="">-- Select Payment Method --</option>
                                        <option value="cash" <?php echo e(old('payment_method')=='cash' ? 'selected' : ''); ?>>Cash</option>
                                        <option value="mpesa" <?php echo e(old('payment_method')=='mpesa' ? 'selected' : ''); ?>>M-Pesa</option>
                                        <option value="bank" <?php echo e(old('payment_method')=='bank' ? 'selected' : ''); ?>>Bank Transfer</option>
                                        <option value="cheque" <?php echo e(old('payment_method')=='cheque' ? 'selected' : ''); ?>>Cheque</option>
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

                                
                                <div class="col-md-6 col-lg-3 mb-4">
                                    <label for="income_date" class="form-label fw-semibold" style="font-size: 14px; color: #495057; margin-bottom: 10px;">
                                        <i class="fa fa-calendar me-2 text-warning"></i>Date <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" 
                                           id="income_date"
                                           name="income_date" 
                                           class="form-control <?php $__errorArgs = ['income_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           value="<?php echo e(old('income_date', date('Y-m-d'))); ?>"
                                           style="border-radius: 8px; border: 1px solid #e0e0e0; padding: 12px 16px; font-size: 14px;"
                                           required>
                                    <?php $__errorArgs = ['income_date'];
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
                            <h5 class="section-title mb-4" style="color: #2c3e50; font-weight: 600; font-size: 16px; display: flex; align-items: center; padding-bottom: 12px; border-bottom: 2px solid #e8eaed;">
                                <i class="fa fa-calendar-alt me-2" style="color: #79c347;"></i>
                                Academic Period
                            </h5>

                            <div class="row">
                                
                                <div class="col-md-6 mb-4">
                                    <label for="term_id" class="form-label fw-semibold" style="font-size: 14px; color: #495057; margin-bottom: 10px;">
                                        <i class="fa fa-bookmark me-2 text-primary"></i>Term <span class="text-danger">*</span>
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
                                            style="border-radius: 8px; border: 1px solid #e0e0e0; padding: 12px 16px; font-size: 14px;"
                                            required>
                                        <option value="">-- Select Term --</option>
                                        <?php $__currentLoopData = $terms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $term): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($term->id); ?>" <?php echo e(old('term_id') == $term->id ? 'selected' : ''); ?>>
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

                        
                        <div class="form-section mb-4">
                            <h5 class="section-title mb-4" style="color: #2c3e50; font-weight: 600; font-size: 16px; display: flex; align-items: center; padding-bottom: 12px; border-bottom: 2px solid #e8eaed;">
                                <i class="fa fa-file-alt me-2" style="color: #79c347;"></i>
                                Additional Details
                            </h5>

                            <div class="row">
                                
                                <div class="col-12 mb-4">
                                    <label for="description" class="form-label fw-semibold" style="font-size: 14px; color: #495057; margin-bottom: 10px;">
                                        <i class="fa fa-align-left me-2 text-secondary"></i>Description
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
                                              rows="4"
                                              placeholder="Enter any additional notes or details about this income"
                                              style="border-radius: 8px; border: 1px solid #e0e0e0; padding: 12px 16px; font-size: 14px;"><?php echo e(old('description')); ?></textarea>
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

                        
                        <div class="form-actions mt-5 pt-4" style="border-top: 1px solid #e8eaed;">
                            <div class="row">
                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-success px-5 py-3 me-3" style="border-radius: 8px; font-weight: 600; font-size: 15px; min-width: 180px;">
                                        <i class="fa fa-check-circle me-2"></i>Save Income
                                    </button>
                                    <a href="<?php echo e(route('other_incomes.index')); ?>" class="btn btn-outline-secondary px-5 py-3" style="border-radius: 8px; font-weight: 600; font-size: 15px; min-width: 180px;">
                                        <i class="fa fa-times-circle me-2"></i>Cancel
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Container Adjustments */
.container-fluid {
    max-width: 100%;
    padding-left: 15px;
    padding-right: 15px;
}

/* Form Control Focus Effects */
.form-control:focus,
.form-select:focus {
    border-color: #79c347;
    box-shadow: 0 0 0 0.2rem rgba(121, 195, 71, 0.15);
}

/* Invalid Feedback Styling */
.invalid-feedback {
    font-size: 13px;
    margin-top: 6px;
}

.is-invalid {
    border-color: #dc3545 !important;
}

/* Input Group Styling */
.input-group-text {
    background: #f8f9fa;
    border-right: none;
}

.input-group .form-control {
    border-left: none;
}

.input-group:focus-within .input-group-text {
    border-color: #79c347;
}

.input-group:focus-within .form-control {
    border-color: #79c347;
}

/* Button Styles */
.btn-success {
    background: linear-gradient(135deg, #79c347 0%, #5fa732 100%);
    border: none;
    transition: all 0.3s ease;
}

.btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(121, 195, 71, 0.3);
}

.btn-outline-secondary {
    border: 2px solid #6c757d;
    color: #6c757d;
    transition: all 0.3s ease;
}

.btn-outline-secondary:hover {
    background: #6c757d;
    color: white;
    transform: translateY(-2px);
}

.btn-secondary {
    background: #6c757d;
    border: none;
    transition: all 0.3s ease;
}

.btn-secondary:hover {
    background: #5a6268;
    transform: translateY(-2px);
}

/* Form Section Styling */
.form-section {
    background: #fafbfc;
    padding: 25px;
    border-radius: 10px;
    border: 1px solid #f0f0f0;
}

/* Placeholder Styling */
.form-control::placeholder,
.form-select::placeholder {
    color: #b0b8c3;
    font-size: 14px;
}

/* Alert Styling */
.alert {
    border-radius: 8px;
}

.alert ul {
    margin-bottom: 0;
}

.alert li {
    margin-bottom: 4px;
}

.alert li:last-child {
    margin-bottom: 0;
}

/* Textarea Styling */
textarea.form-control {
    resize: vertical;
    min-height: 100px;
}

/* Date Input Styling */
input[type="date"]::-webkit-calendar-picker-indicator {
    cursor: pointer;
    opacity: 0.6;
}

input[type="date"]::-webkit-calendar-picker-indicator:hover {
    opacity: 1;
}

/* Responsive Design */
@media (max-width: 768px) {
    .full.inner_elements {
        padding: 25px 20px !important;
    }

    .form-section {
        padding: 20px 15px;
    }

    .btn-success,
    .btn-outline-secondary {
        min-width: 140px !important;
        padding: 12px 20px !important;
        font-size: 14px !important;
    }

    .section-title {
        font-size: 15px !important;
    }

    .full.graph_head {
        padding: 20px 20px !important;
    }

    .full.graph_head h2 {
        font-size: 18px !important;
    }

    .full.graph_head p {
        font-size: 13px !important;
    }
}

@media (max-width: 576px) {
    .btn-success,
    .btn-outline-secondary {
        width: 100%;
        margin-bottom: 10px;
    }

    .btn-success {
        margin-right: 0 !important;
    }
}
</style>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Allan\smart_accountant2\resources\views/income/create.blade.php ENDPATH**/ ?>