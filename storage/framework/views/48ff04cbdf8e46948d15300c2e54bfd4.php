
<?php $__env->startSection('main'); ?>

<?php
    $reportTitles = [
        'trial-balance' => 'Trial Balance',
        'profit-loss' => 'Profit & Loss',
        'balance-sheet' => 'Balance Sheet',
    ];
    $title = $reportTitles[$reportType] ?? 'Financial Report';
    $exportQuery = http_build_query(array_filter([
        'view' => $viewType,
        'report' => $reportType,
        'term_id' => $viewType === 'term' ? $termId : null,
        'academic_year_id' => $viewType === 'year' ? $academicYearId : null,
    ]));
?>

<div class="main-wrapper">
    
    <div class="page-header mb-4">
        <h4 class="mb-1"><?php echo e($title); ?></h4>
        <p class="text-muted mb-0">From general ledger — cash-basis postings from cashbook activity</p>
    </div>

    
    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    
    <ul class="nav report-tabs mb-4">
        <?php $__currentLoopData = $reportTitles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li class="nav-item">
                <a class="nav-link <?php echo e($reportType === $key ? 'active' : ''); ?>"
                   href="<?php echo e(route('reports.financial', array_filter(['report' => $key, 'view' => $viewType, 'term_id' => $viewType === 'term' ? $termId : null, 'academic_year_id' => $viewType === 'year' ? $academicYearId : null]))); ?>">
                    <?php echo e($label); ?>

                </a>
            </li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>

    
    <div class="card filter-card mb-3">
        <div class="card-body">
            <form method="GET" action="<?php echo e(route('reports.financial')); ?>">
                <input type="hidden" name="report" value="<?php echo e($reportType); ?>">
                <div class="row g-3 align-items-end">
                    <div class="col-md-2">
                        <label class="form-label">View</label>
                        <select name="view" class="form-select" onchange="this.form.submit()">
                            <option value="term" <?php echo e($viewType === 'term' ? 'selected' : ''); ?>>Term</option>
                            <option value="year" <?php echo e($viewType === 'year' ? 'selected' : ''); ?>>Academic Year</option>
                        </select>
                    </div>
                    <?php if($viewType === 'term'): ?>
                        <div class="col-md-4">
                            <label class="form-label">Term</label>
                            <select name="term_id" class="form-select" onchange="this.form.submit()">
                                <?php $__currentLoopData = $terms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $term): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($term->id); ?>" <?php echo e((int) $termId === (int) $term->id ? 'selected' : ''); ?>>
                                        <?php echo e($term->name); ?> - <?php echo e($term->year); ?>

                                        <?php if($currentTerm && $term->id === $currentTerm->id): ?> (current) <?php endif; ?>
                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    <?php else: ?>
                        <div class="col-md-4">
                            <label class="form-label">Academic Year</label>
                            <select name="academic_year_id" class="form-select" onchange="this.form.submit()">
                                <?php $__currentLoopData = $academicYears; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($year->id); ?>" <?php echo e((int) $academicYearId === (int) $year->id ? 'selected' : ''); ?>>
                                        <?php echo e($year->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    <?php endif; ?>
                    <div class="col-md-4 text-md-end">
                        <a href="<?php echo e(route('reports.financial.pdf')); ?>?<?php echo e($exportQuery); ?>" class="btn btn-outline-danger">
                            <i class="fa fa-file-pdf me-1"></i>Export PDF
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <p class="text-muted mb-3">
        <strong>Period:</strong> <?php echo e($period['label']); ?>

        (<?php echo e($period['start']->format('d M Y')); ?> – <?php echo e($period['end']->format('d M Y')); ?>)
    </p>

    <?php if($reportType === 'trial-balance'): ?>
        <?php echo $__env->make('reports.financial.partials.trial-balance', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php elseif($reportType === 'profit-loss'): ?>
        <?php echo $__env->make('reports.financial.partials.profit-loss', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php else: ?>
        <?php echo $__env->make('reports.financial.partials.balance-sheet', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?>
</div>

<style>
/* Base Variables — shared with expense form & balance-sheet partial */
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

/* Report Tabs */
.report-tabs {
    border-bottom: 1px solid var(--gray-200);
    gap: 4px;
}

.report-tabs .nav-link {
    border: none;
    border-bottom: 2px solid transparent;
    border-radius: 0;
    color: var(--gray-500);
    font-size: 14px;
    font-weight: 500;
    padding: 10px 16px;
    transition: color 0.2s, border-color 0.2s;
}

.report-tabs .nav-link:hover {
    color: var(--primary-color);
    border-bottom-color: var(--gray-300);
}

.report-tabs .nav-link.active {
    background: transparent;
    color: var(--primary-color);
    border-bottom-color: var(--primary-color);
}

/* Filter Card */
.filter-card {
    border: 1px solid var(--gray-200);
    border-radius: var(--border-radius);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.filter-card .card-body {
    padding: 20px 24px;
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

/* Buttons */
.btn-outline-danger {
    border-radius: var(--border-radius);
    padding: 8px 20px;
    font-size: 14px;
    font-weight: 500;
}

/* Alerts */
.alert {
    border-radius: var(--border-radius);
    border: none;
    padding: 16px;
}

.alert-danger {
    background-color: #fee2e2;
    color: #991b1b;
}

/* Responsive Design */
@media (max-width: 768px) {
    .page-header h4 {
        font-size: 20px;
    }

    .report-tabs {
        flex-wrap: nowrap;
        overflow-x: auto;
        white-space: nowrap;
    }

    .filter-card .card-body {
        padding: 16px;
    }

    .filter-card .text-md-end {
        text-align: left !important;
        margin-top: 8px;
    }
}
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Allan\smart_accountant2\resources\views/reports/financial/index.blade.php ENDPATH**/ ?>