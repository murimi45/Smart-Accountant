<div class="card table-card mb-4">
    <div class="card-header d-flex justify-content-between">
        <h5 class="mb-0">Balance Sheet</h5>
        <span class="text-muted small">As at <?php echo e($period['end']->format('d M Y')); ?></span>
        <?php if($report['balanced']): ?>
            <span class="badge bg-success">Balanced</span>
        <?php else: ?>
            <span class="badge bg-warning text-dark">Check figures</span>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6 class="text-uppercase text-muted mb-3">Assets</h6>
                <table class="table table-sm">
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $report['assets']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($row['account']->name); ?></td>
                                <td class="text-end"><?php echo e(number_format($row['amount'], 2)); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="2" class="text-muted">No asset balances</td></tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr class="fw-bold border-top">
                            <td>Total Assets</td>
                            <td class="text-end"><?php echo e(number_format($report['totalAssets'], 2)); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="col-md-6">
                <h6 class="text-uppercase text-muted mb-3">Liabilities & Equity</h6>
                <table class="table table-sm">
                    <tbody>
                        <?php $__currentLoopData = $report['liabilities']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($row['account']->name); ?></td>
                                <td class="text-end"><?php echo e(number_format($row['amount'], 2)); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php $__currentLoopData = $report['equity']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($row['account']->name); ?></td>
                                <td class="text-end"><?php echo e(number_format($row['amount'], 2)); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php if(abs($report['retainedEarnings']) >= 0.005): ?>
                            <tr>
                                <td>Retained earnings (computed)</td>
                                <td class="text-end"><?php echo e(number_format($report['retainedEarnings'], 2)); ?></td>
                            </tr>
                        <?php endif; ?>
                        <?php if(empty($report['liabilities']) && empty($report['equity']) && abs($report['retainedEarnings']) < 0.005): ?>
                            <tr><td colspan="2" class="text-muted">No liability or equity balances</td></tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr class="fw-bold border-top">
                            <td>Total Liabilities & Equity</td>
                            <td class="text-end"><?php echo e(number_format($report['totalLiabilitiesAndEquity'], 2)); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\Allan\smart_accountant2\resources\views/reports/financial/partials/balance-sheet.blade.php ENDPATH**/ ?>