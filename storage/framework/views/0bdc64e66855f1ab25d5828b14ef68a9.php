<?php $__env->startSection('main'); ?>

<div class="main-wrapper">
    <div class="page-header mb-4">
        <h4 class="mb-1">Fee Waivers & Bursaries</h4>
        <p class="text-muted mb-0">Review waiver requests and approval trail</p>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card filter-card mb-4">
        <div class="card-body">
            <form method="GET" action="<?php echo e(route('waivers.index')); ?>" class="d-flex flex-wrap gap-3 align-items-end">
                <div>
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="pending" <?php echo e(request('status', 'pending') === 'pending' ? 'selected' : ''); ?>>Pending (<?php echo e($pendingCount); ?>)</option>
                        <option value="approved" <?php echo e(request('status') === 'approved' ? 'selected' : ''); ?>>Approved</option>
                        <option value="rejected" <?php echo e(request('status') === 'rejected' ? 'selected' : ''); ?>>Rejected</option>
                        <option value="all" <?php echo e(request('status') === 'all' ? 'selected' : ''); ?>>All</option>
                    </select>
                </div>
                <a href="<?php echo e(route('invoices.index')); ?>" class="btn btn-outline-secondary">
                    <i class="fa fa-arrow-left me-1"></i>Back to Invoices
                </a>
            </form>
        </div>
    </div>

    <div class="card table-card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fa fa-hand-holding-usd me-2"></i>Waiver Requests</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Requested</th>
                            <th>Student</th>
                            <th>Scope</th>
                            <th>Discount</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Trail</th>
                            <?php if(auth()->user()->role === 'admin'): ?>
                                <th></th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $waivers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $waiver): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td>
                                    <div><?php echo e($waiver->requested_at?->format('d M Y H:i')); ?></div>
                                    <small class="text-muted"><?php echo e($waiver->requestedBy?->admin_name); ?></small>
                                </td>
                                <td>
                                    <div><?php echo e($waiver->invoice?->student?->full_name ?? '—'); ?></div>
                                    <small class="text-muted"><?php echo e($waiver->invoice?->student?->admission); ?></small>
                                </td>
                                <td>
                                    <?php if($waiver->scope === 'line'): ?>
                                        Line: <?php echo e(Str::limit($waiver->target_description, 30)); ?>

                                    <?php else: ?>
                                        Whole invoice
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($waiver->discount_type === 'percentage'): ?>
                                        <?php echo e(number_format($waiver->value, 2)); ?>%
                                    <?php else: ?>
                                        KSh <?php echo e(number_format($waiver->value, 2)); ?>

                                    <?php endif; ?>
                                    <?php if($waiver->computed_amount): ?>
                                        <div><small class="text-success">Applied: KSh <?php echo e(number_format($waiver->computed_amount, 2)); ?></small></div>
                                    <?php endif; ?>
                                </td>
                                <td><small title="<?php echo e($waiver->reason); ?>"><?php echo e(Str::limit($waiver->reason, 50)); ?></small></td>
                                <td>
                                    <?php if($waiver->status === 'pending'): ?>
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    <?php elseif($waiver->status === 'approved'): ?>
                                        <span class="badge bg-success">Approved</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Rejected</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($waiver->reviewed_at): ?>
                                        <small>
                                            <?php echo e($waiver->reviewed_at->format('d M Y H:i')); ?><br>
                                            <?php echo e($waiver->reviewedBy?->admin_name); ?>

                                            <?php if($waiver->review_notes): ?>
                                                <br><span class="text-muted" title="<?php echo e($waiver->review_notes); ?>"><?php echo e(Str::limit($waiver->review_notes, 40)); ?></span>
                                            <?php endif; ?>
                                        </small>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <?php if(auth()->user()->role === 'admin'): ?>
                                    <td class="text-end" style="min-width: 220px;">
                                        <?php if($waiver->isPending()): ?>
                                            <form action="<?php echo e(route('waivers.approve', $waiver)); ?>" method="POST" class="mb-2">
                                                <?php echo csrf_field(); ?>
                                                <input type="text" name="review_notes" class="form-control form-control-sm mb-1" placeholder="Approval notes (optional)" maxlength="500">
                                                <button type="submit" class="btn btn-sm btn-success w-100">Approve</button>
                                            </form>
                                            <form action="<?php echo e(route('waivers.reject', $waiver)); ?>" method="POST">
                                                <?php echo csrf_field(); ?>
                                                <input type="text" name="review_notes" class="form-control form-control-sm mb-1" placeholder="Rejection reason (required)" maxlength="500" required>
                                                <button type="submit" class="btn btn-sm btn-outline-danger w-100">Reject</button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="<?php echo e(auth()->user()->role === 'admin' ? 8 : 7); ?>" class="text-center py-4 text-muted">
                                    No waiver requests found.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php if($waivers->hasPages()): ?>
            <div class="card-footer"><?php echo e($waivers->links()); ?></div>
        <?php endif; ?>
    </div>
</div>

<style>
.filter-card, .table-card { border: 1px solid #e5e7eb; border-radius: 8px; }
.table-card .card-header { background: #f9fafb; border-bottom: 1px solid #e5e7eb; padding: 16px 20px; }
.table thead { background: #f9fafb; }
.table th, .table td { padding: 12px 16px; vertical-align: top; font-size: 14px; }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Allan\smart_accountant2\resources\views/waivers/index.blade.php ENDPATH**/ ?>