<div class="card table-card">
    <div class="card-header d-flex justify-content-between">
        <h5 class="mb-0">Trial Balance</h5>
        <?php if($report['balanced']): ?>
            <span class="badge bg-success">Balanced</span>
        <?php else: ?>
            <span class="badge bg-warning text-dark">Out of balance</span>
        <?php endif; ?>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Account</th>
                        <th>Category</th>
                        <th class="text-end">Debit (KSh)</th>
                        <th class="text-end">Credit (KSh)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $report['rows']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($row['account']->name); ?></td>
                            <td><?php echo e(ucfirst($row['category'])); ?></td>
                            <td class="text-end"><?php echo e($row['debit'] > 0 ? number_format($row['debit'], 2) : '-'); ?></td>
                            <td class="text-end"><?php echo e($row['credit'] > 0 ? number_format($row['credit'], 2) : '-'); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">No ledger activity for this period.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <?php if(count($report['rows']) > 0): ?>
                    <tfoot>
                        <tr class="fw-bold">
                            <td colspan="2">Totals</td>
                            <td class="text-end"><?php echo e(number_format($report['totalDebits'], 2)); ?></td>
                            <td class="text-end"><?php echo e(number_format($report['totalCredits'], 2)); ?></td>
                        </tr>
                    </tfoot>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\Allan\smart_accountant2\resources\views/reports/financial/partials/trial-balance.blade.php ENDPATH**/ ?>