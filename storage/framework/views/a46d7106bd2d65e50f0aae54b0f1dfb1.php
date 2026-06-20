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
            <div class="col-md-4">
                <div class="summary-card">
                    <div class="summary-content">
                        <div class="summary-value">KSh <?php echo e(number_format($report['totals']['budget'], 2)); ?></div>
                        <div class="summary-label">Total Budget</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="summary-card">
                    <div class="summary-content">
                        <div class="summary-value">KSh <?php echo e(number_format($report['totals']['actual'], 2)); ?></div>
                        <div class="summary-label">Total Spent</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="summary-card">
                    <div class="summary-content">
                        <div class="summary-value <?php echo e($report['totals']['variance'] >= 0 ? 'text-success' : 'text-danger'); ?>">
                            KSh <?php echo e(number_format($report['totals']['variance'], 2)); ?>

                        </div>
                        <div class="summary-label">Remaining / (Over)</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card table-card">
            <div class="card-header">
                <h5 class="mb-0"><?php echo e($report['term']->name); ?> <?php echo e($report['term']->year); ?></h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
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
                                <tr class="<?php echo e($row['over_budget'] ? 'table-danger' : ''); ?>">
                                    <td><?php echo e($row['category']->name); ?></td>
                                    <td class="text-end"><?php echo e(number_format($row['budget'], 2)); ?></td>
                                    <td class="text-end"><?php echo e(number_format($row['actual'], 2)); ?></td>
                                    <td class="text-end <?php echo e($row['variance'] >= 0 ? 'text-success' : 'text-danger'); ?>">
                                        <?php echo e(number_format($row['variance'], 2)); ?>

                                    </td>
                                    <td class="text-end">
                                        <?php if($row['utilization'] !== null): ?>
                                            <?php echo e($row['utilization']); ?>%
                                        <?php else: ?>
                                            —
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
                                <tr class="fw-bold">
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Allan\smart_accountant2\resources\views/reports/budget-variance/index.blade.php ENDPATH**/ ?>