<?php $__env->startSection('main'); ?>
<div class="main-wrapper">
    <div class="page-header mb-4">
        <h4 class="mb-1">Finance audit log</h4>
        <p class="text-muted mb-0">Who changed fees, recorded payments, reversed payments, and voided invoices</p>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="<?php echo e(route('audit.index')); ?>" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small">Action type</label>
                    <select name="event" class="form-select form-select-sm">
                        <option value="">All actions</option>
                        <?php $__currentLoopData = $eventLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($value); ?>" <?php echo e(request('event') === $value ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">User</label>
                    <select name="user_id" class="form-select form-select-sm">
                        <option value="">All users</option>
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($user->id); ?>" <?php echo e((string) request('user_id') === (string) $user->id ? 'selected' : ''); ?>>
                                <?php echo e($user->admin_name ?? $user->email); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small">Search</label>
                    <input type="text" name="search" class="form-control form-control-sm" value="<?php echo e(request('search')); ?>"
                           placeholder="Student, admission, amount…">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100"><i class="fa fa-filter me-1"></i>Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card table-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fa fa-history me-2"></i>Audit trail</h5>
            <span class="badge bg-light text-dark"><?php echo e($logs->total()); ?> entries</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>When</th>
                            <th>User</th>
                            <th>Action</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $props = $log->properties instanceof \Illuminate\Support\Collection
                                    ? $log->properties->toArray()
                                    : (array) ($log->properties ?? []);
                            ?>
                            <tr>
                                <td class="text-nowrap small"><?php echo e($log->created_at->format('d M Y H:i')); ?></td>
                                <td class="small">
                                    <?php if($log->causer): ?>
                                        <?php echo e($log->causer->admin_name ?? $log->causer->email); ?>

                                    <?php else: ?>
                                        <span class="text-muted">System</span>
                                    <?php endif; ?>
                                </td>
                                <td class="small">
                                    <span class="badge bg-secondary-subtle text-dark">
                                        <?php echo e($eventLabels[$log->event] ?? ($log->event ?? 'Action')); ?>

                                    </span>
                                </td>
                                <td class="small"><?php echo e($log->description); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">No audit entries yet.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php if($logs->hasPages()): ?>
            <div class="card-footer"><?php echo e($logs->links()); ?></div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Allan\smart_accountant2\resources\views/audit/index.blade.php ENDPATH**/ ?>