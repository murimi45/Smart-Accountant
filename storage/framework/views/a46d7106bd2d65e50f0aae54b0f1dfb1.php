

<?php $__env->startSection('main'); ?>
<div class="main-wrapper">
    
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h4 class="mb-1">Budget Variance Report</h4>
                <p class="text-muted mb-0">Budget vs actual spending by expense category</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="<?php echo e(route('budgets.index', ['term_id' => $termId])); ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="fa fa-edit me-1"></i>Edit Budgets
                </a>
            </div>
        </div>
    </div>

    
    <div class="card filter-card mb-4">
        <div class="card-body">
            <form method="GET" action="<?php echo e(route('reports.budget-variance')); ?>" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Term</label>
                    <select name="term_id" class="form-select">
                        <?php $__currentLoopData = $terms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $term): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($term->id); ?>" <?php echo e((int) $termId === (int) $term->id ? 'selected' : ''); ?>>
                                <?php echo e($term->name); ?> - <?php echo e($term->year); ?>

                                <?php if($currentTerm && $term->id === $currentTerm->id): ?> (current) <?php endif; ?>
                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">Apply</button>
                </div>
            </form>
        </div>
    </div>

    <?php if($report): ?>
        
        <div class="row mb-4">
            <div class="col-md-4 mb-3 mb-md-0">
                <div class="summary-card">
                    <div class="summary-icon icon-budget">
                        <i class="fa fa-wallet"></i>
                    </div>
                    <div class="summary-content">
                        <div class="summary-value">KSh <?php echo e(number_format($report['totals']['budget'], 2)); ?></div>
                        <div class="summary-label">Total Budget</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3 mb-md-0">
                <div class="summary-card">
                    <div class="summary-icon icon-spent">
                        <i class="fa fa-receipt"></i>
                    </div>
                    <div class="summary-content">
                        <div class="summary-value">KSh <?php echo e(number_format($report['totals']['actual'], 2)); ?></div>
                        <div class="summary-label">Total Spent</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="summary-card">
                    <div class="summary-icon <?php echo e($report['totals']['variance'] >= 0 ? 'icon-surplus' : 'icon-deficit'); ?>">
                        <i class="fa fa-<?php echo e($report['totals']['variance'] >= 0 ? 'check' : 'exclamation'); ?>"></i>
                    </div>
                    <div class="summary-content">
                        <div class="summary-value <?php echo e($report['totals']['variance'] >= 0 ? 'value-positive' : 'value-negative'); ?>">
                            KSh <?php echo e(number_format($report['totals']['variance'], 2)); ?>

                        </div>
                        <div class="summary-label">Remaining / (Over)</div>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="card form-card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fa fa-table-list me-2"></i><?php echo e($report['term']->name); ?> <?php echo e($report['term']->year); ?>

                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table report-table mb-0">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th class="text-end">Budget</th>
                                <th class="text-end">Actual</th>
                                <th class="text-end">Variance</th>
                                <th class="text-end">Used</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $report['rows']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="<?php echo e($row['over_budget'] ? 'row-over-budget' : ''); ?>">
                                    <td><?php echo e($row['category']->name); ?></td>
                                    <td class="text-end"><?php echo e(number_format($row['budget'], 2)); ?></td>
                                    <td class="text-end"><?php echo e(number_format($row['actual'], 2)); ?></td>
                                    <td class="text-end <?php echo e($row['variance'] >= 0 ? 'value-positive' : 'value-negative'); ?>">
                                        <?php echo e(number_format($row['variance'], 2)); ?>

                                    </td>
                                    <td class="text-end">
                                        <?php if($row['utilization'] !== null): ?>
                                            <span class="badge <?php echo e($row['utilization'] > 100 ? 'badge-over' : ($row['utilization'] >= 80 ? 'badge-watch' : 'badge-good')); ?>">
                                                <?php echo e($row['utilization']); ?>%
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">—</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        No budgets or expenses recorded for this term.
                                        <a href="<?php echo e(route('budgets.index', ['term_id' => $termId])); ?>">Set budgets</a>.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                        <?php if(count($report['rows']) > 0): ?>
                            <tfoot>
                                <tr class="fw-bold total-row">
                                    <td>Totals</td>
                                    <td class="text-end"><?php echo e(number_format($report['totals']['budget'], 2)); ?></td>
                                    <td class="text-end"><?php echo e(number_format($report['totals']['actual'], 2)); ?></td>
                                    <td class="text-end"><?php echo e(number_format($report['totals']['variance'], 2)); ?></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
/* Base Variables — shared across all report & form views */
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

/* Filter Card */
.filter-card {
    border: 1px solid var(--gray-200);
    border-radius: var(--border-radius);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.filter-card .form-label {
    font-size: 13px;
    font-weight: 500;
    color: var(--gray-700);
    margin-bottom: 6px;
}

.filter-card .form-select {
    border: 1px solid var(--gray-300);
    border-radius: var(--border-radius);
    padding: 8px 12px;
    font-size: 14px;
}

.filter-card .form-select:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(54, 169, 226, 0.1);
}

/* Summary Cards */
.summary-card {
    display: flex;
    align-items: center;
    gap: 16px;
    height: 100%;
    background: #fff;
    border: 1px solid var(--gray-200);
    border-radius: var(--border-radius);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    padding: 20px;
}

.summary-icon {
    flex-shrink: 0;
    width: 44px;
    height: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: var(--border-radius);
    font-size: 16px;
}

.icon-budget {
    background: rgba(54, 169, 226, 0.1);
    color: var(--primary-color);
}

.icon-spent {
    background: rgba(239, 68, 68, 0.1);
    color: var(--danger-color);
}

.icon-surplus {
    background: rgba(121, 195, 71, 0.12);
    color: var(--success-dark);
}

.icon-deficit {
    background: rgba(239, 68, 68, 0.1);
    color: var(--danger-color);
}

.summary-value {
    font-size: 20px;
    font-weight: 700;
    color: var(--gray-900);
    line-height: 1.2;
}

.value-positive {
    color: var(--success-dark);
}

.value-negative {
    color: var(--danger-color);
}

.summary-label {
    font-size: 13px;
    color: var(--gray-500);
    margin-top: 2px;
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

/* Report Table */
.report-table {
    font-size: 14px;
    color: var(--gray-700);
}

.report-table thead th {
    background: var(--gray-50);
    border-bottom: 1px solid var(--gray-200);
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    color: var(--gray-600);
    padding: 12px 24px;
}

.report-table td {
    padding: 10px 24px;
    border-color: var(--gray-100);
}

.report-table tfoot .total-row td {
    background: var(--gray-50);
    border-top: 2px solid var(--gray-300);
    border-bottom: none;
    color: var(--gray-900);
}

.row-over-budget td {
    background: rgba(239, 68, 68, 0.06);
}

/* Utilization Badges */
.badge-good,
.badge-watch,
.badge-over {
    font-size: 12px;
    font-weight: 500;
    padding: 4px 10px;
    border-radius: var(--border-radius);
}

.badge-good {
    background-color: #e8f5e1;
    color: var(--success-dark);
}

.badge-watch {
    background-color: #fef3c7;
    color: #92400e;
}

.badge-over {
    background-color: #fee2e2;
    color: #991b1b;
}

/* Responsive Design */
@media (max-width: 768px) {
    .page-header h4 {
        font-size: 20px;
    }

    .summary-card {
        padding: 16px;
    }

    .summary-value {
        font-size: 18px;
    }

    .report-table thead th,
    .report-table td {
        padding: 10px 16px;
    }
}
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Allan\smart_accountant2\resources\views/reports/budget-variance/index.blade.php ENDPATH**/ ?>