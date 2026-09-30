<?php $__env->startSection('main'); ?>
<div class="main-wrapper">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h4 class="mb-1">Employees</h4>
                <p class="text-muted mb-0">Staff paid through payroll</p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <a href="<?php echo e(route('bulk.index')); ?>" class="btn btn-outline-secondary me-2">
                    <i class="fa fa-upload me-2"></i>Import CSV
                </a>
                <a href="<?php echo e(route('employees.create')); ?>" class="btn btn-success">
                    <i class="fa fa-plus me-2"></i>Add Employee
                </a>
            </div>
        </div>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show mb-4">
            <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>Staff no.</th>
                            <th>Name</th>
                            <th>Grade</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($employee->staff_number); ?></td>
                                <td><?php echo e($employee->full_name); ?></td>
                                <td><?php echo e($employee->grade->name ?? '—'); ?></td>
                                <td><?php echo e($employee->phone ?: '—'); ?></td>
                                <td><?php echo e(ucfirst($employee->status)); ?></td>
                                <td class="text-end">
                                    <a href="<?php echo e(route('employees.edit', $employee->id)); ?>" class="btn btn-sm btn-light">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">No employees yet. Add one, or import a CSV from Bulk Import / Export.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php if($employees->hasPages()): ?>
            <div class="card-footer"><?php echo e($employees->links()); ?></div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Allan\smart_accountant2\resources\views/payroll/employees/index.blade.php ENDPATH**/ ?>