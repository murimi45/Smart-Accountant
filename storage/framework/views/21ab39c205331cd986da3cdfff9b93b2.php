<div class="card form-card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h5 class="mb-0">
            <i class="fa fa-balance-scale me-2"></i>Balance Sheet
        </h5>
        <div class="d-flex align-items-center gap-3">
            <span class="text-muted small">As at <?php echo e($period['end']->format('d M Y')); ?></span>
            <?php if($report['balanced']): ?>
                <span class="badge badge-balanced">
                    <i class="fa fa-check-circle me-1"></i>Balanced
                </span>
            <?php else: ?>
                <span class="badge badge-check">
                    <i class="fa fa-exclamation-triangle me-1"></i>Check figures
                </span>
            <?php endif; ?>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            
            <div class="form-section col-md-6 mb-4 mb-md-0">
                <h6 class="section-title mb-3">
                    <i class="fa fa-coins me-2"></i>Assets
                </h6>
                <table class="table report-table mb-0">
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
                        <tr class="fw-bold total-row">
                            <td>Total Assets</td>
                            <td class="text-end"><?php echo e(number_format($report['totalAssets'], 2)); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            
            <div class="form-section col-md-6">
                <h6 class="section-title mb-3">
                    <i class="fa fa-balance-scale-right me-2"></i>Liabilities &amp; Equity
                </h6>
                <table class="table report-table mb-0">
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
                        <tr class="fw-bold total-row">
                            <td>Total Liabilities &amp; Equity</td>
                            <td class="text-end"><?php echo e(number_format($report['totalLiabilitiesAndEquity'], 2)); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
/* Base Variables — shared with expense form, profit-loss, trial-balance & financial-report wrapper */
:root {
    --primary-color: #36a9e2;
    --success-color: #79c347;
    --success-dark: #5fa732;
    --danger-color: #ef4444;
    --warning-color: #f59e0b;
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

/* Report Card */
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
.section-title {
    font-size: 15px;
    font-weight: 600;
    color: var(--gray-700);
    padding-bottom: 12px;
    border-bottom: 1px solid var(--gray-200);
}

/* Status Badges */
.badge-balanced,
.badge-check {
    font-size: 13px;
    font-weight: 500;
    padding: 6px 12px;
    border-radius: var(--border-radius);
}

.badge-balanced {
    background-color: #e8f5e1;
    color: var(--success-dark);
}

.badge-check {
    background-color: #fef3c7;
    color: #92400e;
}

/* Report Table */
.report-table {
    font-size: 14px;
    color: var(--gray-700);
}

.report-table td {
    padding: 8px 4px;
    border-color: var(--gray-100);
}

.report-table tbody tr:last-child td {
    border-bottom: none;
}

.report-table tfoot .total-row td {
    border-top: 2px solid var(--gray-300);
    border-bottom: none;
    padding-top: 12px;
    color: var(--gray-900);
}

/* Responsive Design */
@media (max-width: 768px) {
    .form-card .card-body {
        padding: 24px 16px;
    }

    .form-card .card-header {
        align-items: flex-start;
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
</style><?php /**PATH C:\Users\Allan\smart_accountant2\resources\views/reports/financial/partials/balance-sheet.blade.php ENDPATH**/ ?>