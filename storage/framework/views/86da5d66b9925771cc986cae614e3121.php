<?php $__env->startSection('main'); ?>
<?php $editing = $employee->exists; ?>
<div class="main-wrapper">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h4 class="mb-1"><?php echo e($editing ? 'Edit Employee' : 'Add Employee'); ?></h4>
                <p class="text-muted mb-0">Identity, statutory numbers, and current grade</p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <a href="<?php echo e(route('employees.index')); ?>" class="btn btn-secondary">
                    <i class="fa fa-arrow-left me-2"></i>Back to List
                </a>
            </div>
        </div>
    </div>

    <?php if($errors->any()): ?>
        <div class="alert alert-danger mb-4">
            <ul class="mb-0">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show mb-4">
        <?php echo e(session('success')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form action="<?php echo e($editing ? route('employees.update', $employee->id) : route('employees.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php if($editing): ?>
                    <?php echo method_field('PUT'); ?>
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Staff number <span class="text-danger">*</span></label>
                        <input type="text" name="staff_number" class="form-control" required value="<?php echo e(old('staff_number', $employee->staff_number)); ?>">
                    </div>
                    <div class="col-md-8 mb-3">
                        <label class="form-label">Full name <span class="text-danger">*</span></label>
                        <input type="text" name="full_name" class="form-control" required value="<?php echo e(old('full_name', $employee->full_name)); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control" value="<?php echo e(old('phone', $employee->phone)); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <?php $__currentLoopData = ['active' => 'Active', 'left' => 'Left']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($value); ?>" <?php if(old('status', $employee->status) === $value): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Start date</label>
                        <input type="date" name="start_date" class="form-control" value="<?php echo e(old('start_date', optional($employee->start_date)->format('Y-m-d'))); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Salary grade</label>
                        <select name="salary_grade_id" class="form-select">
                            <option value="">No grade yet</option>
                            <?php $__currentLoopData = $grades; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $grade): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($grade->id); ?>" <?php if((string) old('salary_grade_id', $employee->salary_grade_id) === (string) $grade->id): echo 'selected'; endif; ?>>
                                    <?php echo e($grade->name); ?> — KES <?php echo e(number_format($grade->basic_pay, 2)); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">KRA PIN</label>
                        <input type="text" name="kra_pin" class="form-control" value="<?php echo e(old('kra_pin', $employee->kra_pin)); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">NSSF number</label>
                        <input type="text" name="nssf_number" class="form-control" value="<?php echo e(old('nssf_number', $employee->nssf_number)); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">SHIF number</label>
                        <input type="text" name="shif_number" class="form-control" value="<?php echo e(old('shif_number', $employee->shif_number)); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Pay method</label>
                        <select name="payment_method" class="form-select">
                            <option value="">Not set</option>
                            <?php $__currentLoopData = ['bank' => 'Bank', 'mpesa' => 'M-Pesa', 'cash' => 'Cash']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($value); ?>" <?php if(old('payment_method', $employee->payment_method) === $value): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Bank name</label>
                        <input type="text" name="bank_name" class="form-control" value="<?php echo e(old('bank_name', $employee->bank_name)); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Account or M-Pesa number</label>
                        <input type="text" name="account_number" class="form-control" value="<?php echo e(old('account_number', $employee->account_number)); ?>">
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="<?php echo e(route('employees.index')); ?>" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-success"><?php echo e($editing ? 'Update Employee' : 'Save Employee'); ?></button>
                </div>
            </form>
        </div>
    </div>

    <?php if($editing): ?>
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">Deductions</h5>
            </div>
            <div class="card-body">
                <p class="text-muted small">Recurring deductions repeat every month. A balance charges the installment until the remaining amount is used up. These come off after PAYE.</p>

                <?php $__empty_1 = true; $__currentLoopData = $employee->deductions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $deduction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <form action="<?php echo e(route('employees.deductions.update', [$employee->id, $deduction->id])); ?>" method="POST" class="border rounded p-3 mb-3">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>
                        <div class="row align-items-end">
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Name</label>
                                <input type="text" name="name" class="form-control" required value="<?php echo e($deduction->name); ?>" <?php if(! $deduction->is_active): echo 'disabled'; endif; ?>>
                            </div>
                            <div class="col-md-2 mb-2">
                                <label class="form-label">Kind</label>
                                <select name="kind" class="form-select" <?php if(! $deduction->is_active): echo 'disabled'; endif; ?>>
                                    <option value="recurring" <?php if($deduction->kind === 'recurring'): echo 'selected'; endif; ?>>Recurring</option>
                                    <option value="balance" <?php if($deduction->kind === 'balance'): echo 'selected'; endif; ?>>Balance</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-2">
                                <label class="form-label">Monthly amount</label>
                                <input type="number" name="amount" class="form-control" min="0.01" step="0.01" required value="<?php echo e($deduction->amount); ?>" <?php if(! $deduction->is_active): echo 'disabled'; endif; ?>>
                            </div>
                            <div class="col-md-2 mb-2">
                                <label class="form-label">Balance left</label>
                                <input type="number" name="balance_remaining" class="form-control" min="0.01" step="0.01" value="<?php echo e($deduction->balance_remaining); ?>" <?php if(! $deduction->is_active): echo 'disabled'; endif; ?>>
                            </div>
                            <div class="col-md-3 mb-2 text-md-end">
                                <?php if($deduction->is_active): ?>
                                    <button type="submit" class="btn btn-success">Save</button>
                                <?php else: ?>
                                    <span class="text-muted">Stopped</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                    <?php if($deduction->is_active): ?>
                        <form action="<?php echo e(route('employees.deductions.stop', [$employee->id, $deduction->id])); ?>" method="POST" class="mb-4">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Stop this deduction?')">Stop</button>
                        </form>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-muted">No deductions yet.</p>
                <?php endif; ?>

                <hr>
                <h6 class="mb-3">Add a deduction</h6>
                <form action="<?php echo e(route('employees.deductions.store', $employee->id)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="row align-items-end">
                        <div class="col-md-3 mb-2">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" required placeholder="SACCO, advance, HELB">
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label">Kind</label>
                            <select name="kind" class="form-select" required>
                                <option value="recurring">Recurring</option>
                                <option value="balance">Balance</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <label class="form-label">Monthly amount</label>
                            <input type="number" name="amount" class="form-control" min="0.01" step="0.01" required>
                        </div>
                        <div class="col-md-2 mb-2">
                            <label class="form-label">Balance left</label>
                            <input type="number" name="balance_remaining" class="form-control" min="0.01" step="0.01" placeholder="Balance only">
                        </div>
                        <div class="col-md-2 mb-2">
                            <button type="submit" class="btn btn-success w-100">Add</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Allan\smart_accountant2\resources\views/payroll/employees/form.blade.php ENDPATH**/ ?>