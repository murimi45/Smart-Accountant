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
        <div class="alert alert-danger mb-4"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <ul class="nav nav-tabs mb-4">
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
                            <select name="term_id" class="form-select">
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
                            <select name="academic_year_id" class="form-select">
                                <?php $__currentLoopData = $academicYears; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($year->id); ?>" <?php echo e((int) $academicYearId === (int) $year->id ? 'selected' : ''); ?>>
                                        <?php echo e($year->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    <?php endif; ?>

                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary"><i class="fa fa-filter me-1"></i>Apply</button>
                        <a href="<?php echo e(route('reports.financial', ['report' => $reportType])); ?>" class="btn btn-outline-secondary">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card actions-card mb-4">
        <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <span class="text-muted">Period:</span>
                <strong><?php echo e($period['label']); ?></strong>
                <span class="text-muted ms-3"><?php echo e($period['start']->format('d M Y')); ?> – <?php echo e($period['end']->format('d M Y')); ?></span>
            </div>
            <a href="<?php echo e(route('reports.financial.pdf')); ?>?<?php echo e($exportQuery); ?>" class="btn btn-outline-danger btn-sm">
                <i class="fa fa-file-pdf me-1"></i>Export PDF
            </a>
        </div>
    </div>

    <?php if($reportType === 'trial-balance'): ?>
        <?php echo $__env->make('reports.financial.partials.trial-balance', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php elseif($reportType === 'profit-loss'): ?>
        <?php echo $__env->make('reports.financial.partials.profit-loss', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php else: ?>
        <?php echo $__env->make('reports.financial.partials.balance-sheet', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?>

    <div class="alert alert-info mt-4 small">
        <i class="fa fa-info-circle me-1"></i>
        Student fee receivables are not on this balance sheet (cash-basis ledger). Use
        <a href="<?php echo e(route('reports.aged-debtors')); ?>">Aged Debtors</a> for outstanding fee balances.
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Allan\smart_accountant2\resources\views/reports/financial/index.blade.php ENDPATH**/ ?>