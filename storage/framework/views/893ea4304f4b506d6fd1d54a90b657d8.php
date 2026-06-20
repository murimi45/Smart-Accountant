<?php $__env->startSection('main'); ?>

<div class="main-wrapper">
    
    <div class="page-header mb-4">
        <h4 class="mb-1">Aged Debtors Report</h4>
        <p class="text-muted mb-0">Outstanding fee balances by age, filtered by class and term</p>
    </div>

    
    <div class="card filter-card mb-3">
        <div class="card-body">
            <form method="GET" action="<?php echo e(route('reports.aged-debtors')); ?>" class="filter-form">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label"><i class="fa fa-school me-1"></i>Class</label>
                        <select name="class_id" class="form-select">
                            <option value="">All Classes</option>
                            <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($class->id); ?>" <?php echo e((int) $classId === (int) $class->id ? 'selected' : ''); ?>>
                                    <?php echo e($class->name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label"><i class="fa fa-calendar me-1"></i>Term</label>
                        <select name="term_id" class="form-select">
                            <?php $__currentLoopData = $terms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $term): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($term->id); ?>" <?php echo e((int) $termId === (int) $term->id ? 'selected' : ''); ?>>
                                    <?php echo e($term->name); ?> - <?php echo e($term->year); ?>

                                    <?php if($currentTerm && $term->id === $currentTerm->id): ?> (current) <?php endif; ?>
                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-filter me-1"></i>Apply Filters
                            </button>
                            <a href="<?php echo e(route('reports.aged-debtors')); ?>" class="btn btn-outline-secondary" title="Reset">
                                <i class="fa fa-redo"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    
    <div class="card actions-card mb-4">
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between">
                <div>
                    <span class="text-muted me-2"><i class="fa fa-info-circle me-1"></i>As at:</span>
                    <strong><?php echo e($report['asOf']->format('d M Y')); ?></strong>
                    <?php if($selectedTerm): ?>
                        <span class="text-muted ms-3">Term: <strong><?php echo e($selectedTerm->name); ?> <?php echo e($selectedTerm->year); ?></strong></span>
                    <?php endif; ?>
                    <?php if($selectedClass): ?>
                        <span class="text-muted ms-3">Class: <strong><?php echo e($selectedClass->name); ?></strong></span>
                    <?php endif; ?>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <?php
                        $exportQuery = http_build_query(array_filter([
                            'term_id' => $termId,
                            'class_id' => $classId,
                        ]));
                    ?>
                    <a href="<?php echo e(route('reports.aged-debtors.pdf')); ?>?<?php echo e($exportQuery); ?>" class="btn btn-outline-danger btn-action">
                        <i class="fa fa-file-pdf me-1"></i>Export PDF
                    </a>
                    <a href="<?php echo e(route('reports.aged-debtors.excel')); ?>?<?php echo e($exportQuery); ?>" class="btn btn-outline-success btn-action">
                        <i class="fa fa-file-excel me-1"></i>Export Excel
                    </a>
                </div>
            </div>
        </div>
    </div>

    
    <div class="row mb-4">
        <div class="col-md-3 mb-3 mb-md-0">
            <div class="summary-card">
                <div class="summary-icon icon-debtors">
                    <i class="fa fa-users"></i>
                </div>
                <div class="summary-content">
                    <div class="summary-value"><?php echo e(number_format($report['debtorCount'])); ?></div>
                    <div class="summary-label">Debtors</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3 mb-md-0">
            <div class="summary-card">
                <div class="summary-icon icon-outstanding">
                    <i class="fa fa-money-bill-wave"></i>
                </div>
                <div class="summary-content">
                    <div class="summary-value">KSh <?php echo e(number_format($report['totals']['total_balance'], 2)); ?></div>
                    <div class="summary-label">Total Outstanding</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3 mb-md-0">
            <div class="summary-card">
                <div class="summary-icon icon-overdue">
                    <i class="fa fa-clock"></i>
                </div>
                <div class="summary-content">
                    <div class="summary-value">KSh <?php echo e(number_format($report['totals']['days_91_plus'], 2)); ?></div>
                    <div class="summary-label">91+ Days Overdue</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-card">
                <div class="summary-icon icon-current">
                    <i class="fa fa-check-circle"></i>
                </div>
                <div class="summary-content">
                    <div class="summary-value">KSh <?php echo e(number_format($report['totals']['current'], 2)); ?></div>
                    <div class="summary-label">Current (0–30 days)</div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="card form-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fa fa-table me-2"></i>Aging Detail</h5>
            <span class="badge badge-neutral"><?php echo e($report['debtorCount']); ?> rows</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table report-table report-table-compact mb-0">
                    <thead>
                        <tr>
                            <th>Admission</th>
                            <th>Student</th>
                            <th>Class</th>
                            <th>Term</th>
                            <th>Oldest Invoice</th>
                            <th>Days</th>
                            <th class="text-end">Current</th>
                            <th class="text-end">31–60</th>
                            <th class="text-end">61–90</th>
                            <th class="text-end">91+</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $report['rows']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($row['admission']); ?></td>
                                <td><?php echo e($row['student_name']); ?></td>
                                <td><?php echo e($row['class_name']); ?></td>
                                <td><?php echo e($row['term_name']); ?></td>
                                <td><?php echo e($row['invoice_date'] ? \Carbon\Carbon::parse($row['invoice_date'])->format('d M Y') : '—'); ?></td>
                                <td><?php echo e($row['days_outstanding']); ?></td>
                                <td class="text-end"><?php echo e(number_format($row['buckets']['current'], 2)); ?></td>
                                <td class="text-end"><?php echo e(number_format($row['buckets']['days_31_60'], 2)); ?></td>
                                <td class="text-end"><?php echo e(number_format($row['buckets']['days_61_90'], 2)); ?></td>
                                <td class="text-end <?php echo e($row['buckets']['days_91_plus'] > 0 ? 'value-negative fw-semibold' : ''); ?>">
                                    <?php echo e(number_format($row['buckets']['days_91_plus'], 2)); ?>

                                </td>
                                <td class="text-end fw-semibold"><?php echo e(number_format($row['total_balance'], 2)); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="11" class="text-center py-4 text-muted">
                                    No outstanding balances for the selected filters.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                    <?php if(count($report['rows']) > 0): ?>
                        <tfoot>
                            <tr class="fw-bold total-row">
                                <td colspan="6" class="text-end">Totals</td>
                                <td class="text-end"><?php echo e(number_format($report['totals']['current'], 2)); ?></td>
                                <td class="text-end"><?php echo e(number_format($report['totals']['days_31_60'], 2)); ?></td>
                                <td class="text-end"><?php echo e(number_format($report['totals']['days_61_90'], 2)); ?></td>
                                <td class="text-end"><?php echo e(number_format($report['totals']['days_91_plus'], 2)); ?></td>
                                <td class="text-end"><?php echo e(number_format($report['totals']['total_balance'], 2)); ?></td>
                            </tr>
                        </tfoot>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>
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

/* Filter & Actions Cards */
.filter-card,
.actions-card {
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

.btn-action {
    font-size: 14px;
    border-radius: var(--border-radius);
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

.icon-debtors {
    background: rgba(239, 68, 68, 0.1);
    color: var(--danger-color);
}

.icon-outstanding {
    background: rgba(245, 158, 11, 0.12);
    color: var(--warning-color);
}

.icon-overdue {
    background: rgba(220, 38, 38, 0.1);
    color: #dc2626;
}

.icon-current {
    background: rgba(121, 195, 71, 0.12);
    color: var(--success-color);
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

/* Neutral Badge (row count, etc.) */
.badge-neutral {
    background-color: var(--gray-100);
    color: var(--gray-700);
    font-size: 13px;
    font-weight: 500;
    padding: 6px 12px;
    border-radius: var(--border-radius);
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
    vertical-align: middle;
}

.report-table tfoot .total-row td {
    background: var(--gray-50);
    border-top: 2px solid var(--gray-300);
    border-bottom: none;
    color: var(--gray-900);
}

/* Compact variant for wide, many-column tables */
.report-table-compact thead th,
.report-table-compact td {
    padding: 10px 16px;
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
    .report-table td,
    .report-table-compact thead th,
    .report-table-compact td {
        padding: 10px 12px;
    }
}
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Allan\smart_accountant2\resources\views/reports/aged-debtors/index.blade.php ENDPATH**/ ?>