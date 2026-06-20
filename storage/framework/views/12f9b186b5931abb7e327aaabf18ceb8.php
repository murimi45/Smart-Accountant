<?php $__env->startSection('main'); ?>
<div class="main-wrapper">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h4 class="mb-1">Expense Budgets</h4>
                <p class="text-muted mb-0">Set spending limits per category for each term</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="<?php echo e(route('reports.budget-variance', ['term_id' => $termId])); ?>" class="btn btn-outline-primary btn-sm">
                    <i class="fa fa-chart-bar me-1"></i>Variance Report
                </a>
            </div>
        </div>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show mb-4"><?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card filter-card mb-4">
        <div class="card-body">
            <form method="GET" action="<?php echo e(route('budgets.index')); ?>" class="row g-3 align-items-end">
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
            </form>
        </div>
    </div>

    <?php if($selectedTerm): ?>
        <form method="POST" action="<?php echo e(route('budgets.update')); ?>">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <input type="hidden" name="term_id" value="<?php echo e($termId); ?>">

            <div class="card table-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Budget for <?php echo e($selectedTerm->name); ?> <?php echo e($selectedTerm->year); ?></h5>
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="fa fa-save me-1"></i>Save Budgets
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th style="width: 180px;">Budget (KSh)</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <?php $budget = $budgets->get($category->id); ?>
                                    <tr>
                                        <td><?php echo e($category->name); ?></td>
                                        <td>
                                            <input type="number"
                                                   name="amounts[<?php echo e($category->id); ?>]"
                                                   class="form-control form-control-sm"
                                                   min="0"
                                                   step="0.01"
                                                   value="<?php echo e(old('amounts.'.$category->id, $budget?->amount)); ?>"
                                                   placeholder="0.00">
                                        </td>
                                        <td>
                                            <input type="text"
                                                   name="notes[<?php echo e($category->id); ?>]"
                                                   class="form-control form-control-sm"
                                                   value="<?php echo e(old('notes.'.$category->id, $budget?->notes)); ?>"
                                                   placeholder="Optional note">
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted">
                                            No expense categories yet.
                                            <a href="<?php echo e(route('expense_categories.index')); ?>">Add categories</a> first.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </form>
    <?php else: ?>
        <div class="alert alert-warning">Select a term to set budgets.</div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Allan\smart_accountant2\resources\views/budgets/index.blade.php ENDPATH**/ ?>