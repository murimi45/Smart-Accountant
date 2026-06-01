

<?php $__env->startSection('main'); ?>
<div class="main-wrapper">
    
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h4 class="mb-1">Other Incomes</h4>
                <p class="text-muted mb-0">Track and manage additional income sources</p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <a href="<?php echo e(route('other_incomes.create')); ?>" class="btn btn-success">
                    <i class="fa fa-plus me-2"></i>Add New Income
                </a>
            </div>
        </div>
    </div>

    
    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="fa fa-check-circle me-2"></i>
            <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="fa fa-exclamation-circle me-2"></i>
            <?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
            <div class="summary-card">
                <div class="summary-icon" style="background-color: #e8f5e0;">
                    <i class="fa fa-money-bill-wave" style="color: #79c347;"></i>
                </div>
                <div class="summary-content">
                    <div class="summary-value">KSh <?php echo e(number_format($incomes->sum('amount'), 2)); ?></div>
                    <div class="summary-label">Total Income</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
            <div class="summary-card">
                <div class="summary-icon" style="background-color: #e0f2fe;">
                    <i class="fa fa-calendar-check" style="color: #36a9e2;"></i>
                </div>
                <div class="summary-content">
                    <div class="summary-value"><?php echo e($incomes->count()); ?></div>
                    <div class="summary-label">Total Entries</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3 mb-md-0">
            <div class="summary-card">
                <div class="summary-icon" style="background-color: #fef3c7;">
                    <i class="fa fa-clock" style="color: #f59e0b;"></i>
                </div>
                <div class="summary-content">
                    <div class="summary-value">KSh <?php echo e(number_format($incomes->where('income_date', '>=', now()->startOfMonth())->sum('amount'), 2)); ?></div>
                    <div class="summary-label">This Month</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="summary-card">
                <div class="summary-icon" style="background-color: #f3e8ff;">
                    <i class="fa fa-tags" style="color: #a855f7;"></i>
                </div>
                <div class="summary-content">
                    <div class="summary-value"><?php echo e($incomes->groupBy('income_category_id')->count()); ?></div>
                    <div class="summary-label">Categories Used</div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="card filter-card mb-4">
        <div class="card-body">
            <form method="GET" action="<?php echo e(route('other_incomes.index')); ?>" class="filter-form">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label"><i class="fa fa-tag me-1"></i>Category</label>
                        <select name="category_id" class="form-select">
                            <option value="">All Categories</option>
                            <?php $__currentLoopData = $categories ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($cat->id); ?>" <?php echo e(request('category_id') == $cat->id ? 'selected' : ''); ?>>
                                    <?php echo e($cat->name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label"><i class="fa fa-credit-card me-1"></i>Payment Method</label>
                        <select name="payment_method" class="form-select">
                            <option value="">All Methods</option>
                            <option value="cash" <?php echo e(request('payment_method') == 'cash' ? 'selected' : ''); ?>>Cash</option>
                            <option value="mpesa" <?php echo e(request('payment_method') == 'mpesa' ? 'selected' : ''); ?>>M-Pesa</option>
                            <option value="bank" <?php echo e(request('payment_method') == 'bank' ? 'selected' : ''); ?>>Bank Transfer</option>
                            <option value="cheque" <?php echo e(request('payment_method') == 'cheque' ? 'selected' : ''); ?>>Cheque</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label"><i class="fa fa-search me-1"></i>Search Description</label>
                        <input type="text" 
                               name="search" 
                               class="form-control" 
                               value="<?php echo e(request('search')); ?>"
                               placeholder="Enter description keyword">
                    </div>

                    <div class="col-md-2">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="fa fa-filter me-1"></i>Filter
                            </button>
                            <a href="<?php echo e(route('other_incomes.index')); ?>" class="btn btn-outline-secondary" title="Reset">
                                <i class="fa fa-redo"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    
    <div class="card table-card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fa fa-list me-2"></i>Income Records
                </h5>
                <span class="badge bg-light text-dark"><?php echo e($incomes->count()); ?> Entries</span>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table income-table mb-0">
                    <thead>
                        <tr>
                            <th style="width: 60px;">#</th>
                            <th>Category</th>
                            <th>Amount</th>
                            <th>Payment Method</th>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $incomes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $income): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <span class="row-number"><?php echo e($loop->iteration); ?></span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="category-icon">
                                        <?php echo e(strtoupper(substr($income->category->name ?? 'N', 0, 1))); ?>

                                    </div>
                                    <div class="ms-3">
                                        <div class="category-name"><?php echo e($income->category->name ?? 'N/A'); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="amount-text text-success">
                                    KSh <?php echo e(number_format($income->amount, 2)); ?>

                                </span>
                            </td>
                            <td>
                                <span class="badge-method"><?php echo e(ucfirst($income->payment_method ?? '-')); ?></span>
                            </td>
                            <td>
                                <span class="date-text">
                                    <i class="fa fa-calendar-day me-1"></i>
                                    <?php echo e($income->income_date ? \Carbon\Carbon::parse($income->income_date)->format('d M, Y') : '-'); ?>

                                </span>
                            </td>
                            <td>
                                <?php if($income->description): ?>
                                    <span class="description-text"><?php echo e(Str::limit($income->description, 50)); ?></span>
                                <?php else: ?>
                                    <span class="text-muted description-empty">
                                        <i class="fa fa-minus me-1"></i>No description
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="<?php echo e(route('other_incomes.edit', $income->id)); ?>" 
                                       class="btn btn-sm btn-light" 
                                       title="Edit Income">
                                        <i class="fa fa-edit"></i>
                                    </a>

                                    <form action="<?php echo e(route('other_incomes.destroy', $income->id)); ?>" 
                                          method="POST" 
                                          class="d-inline"
                                          onsubmit="return confirm('Are you sure you want to delete this income record?')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button class="btn btn-sm btn-danger" 
                                                title="Delete Income">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fa fa-inbox fa-3x mb-3"></i>
                                    <p class="mb-2">No Income Records Found</p>
                                    <small class="d-block mb-3">Start by adding your first income entry</small>
                                    <a href="<?php echo e(route('other_incomes.create')); ?>" class="btn btn-success">
                                        <i class="fa fa-plus me-2"></i>Add New Income
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        
        <?php if(method_exists($incomes, 'hasPages') && $incomes->hasPages()): ?>
        <div class="card-footer">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div class="mb-2 mb-sm-0">
                    <small class="text-muted">
                        Showing <?php echo e($incomes->firstItem()); ?> to <?php echo e($incomes->lastItem()); ?> of <?php echo e($incomes->total()); ?> entries
                    </small>
                </div>
                <div>
                    <?php echo e($incomes->links()); ?>

                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
/* Base Variables */
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

/* Summary Cards */
.summary-card {
    background: white;
    border: 1px solid var(--gray-200);
    border-radius: var(--border-radius);
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 16px;
    transition: all 0.2s;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.summary-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    transform: translateY(-2px);
}

.summary-icon {
    width: 56px;
    height: 56px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.summary-icon i {
    font-size: 24px;
}

.summary-content {
    flex: 1;
    min-width: 0;
}

.summary-value {
    font-size: 20px;
    font-weight: 700;
    color: var(--gray-900);
    margin-bottom: 4px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.summary-label {
    font-size: 13px;
    color: var(--gray-500);
    font-weight: 500;
}

/* Cards */
.filter-card,
.table-card {
    border: 1px solid var(--gray-200);
    border-radius: var(--border-radius);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.filter-card .card-body {
    padding: 20px;
}

.table-card .card-header {
    background: var(--gray-50);
    border-bottom: 1px solid var(--gray-200);
    padding: 16px 20px;
}

.card-footer {
    background: var(--gray-50);
    border-top: 1px solid var(--gray-200);
    padding: 16px 20px;
}

/* Form Elements */
.form-label {
    font-size: 13px;
    font-weight: 500;
    color: var(--gray-700);
    margin-bottom: 6px;
}

.form-control,
.form-select {
    border: 1px solid var(--gray-300);
    border-radius: var(--border-radius);
    padding: 8px 12px;
    font-size: 14px;
    transition: border-color 0.2s;
}

.form-control:focus,
.form-select:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(54, 169, 226, 0.1);
}

/* Buttons */
.btn {
    border-radius: var(--border-radius);
    padding: 8px 16px;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.2s;
}

.btn-primary {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
}

.btn-primary:hover {
    background-color: #2a8cbd;
    border-color: #2a8cbd;
}

.btn-success {
    background-color: var(--success-color);
    border-color: var(--success-color);
}

.btn-success:hover {
    background-color: var(--success-dark);
    border-color: var(--success-dark);
}

.btn-danger {
    background-color: var(--danger-color);
    border-color: var(--danger-color);
}

.btn-danger:hover {
    background-color: #dc2626;
    border-color: #dc2626;
}

.btn-outline-secondary {
    color: var(--gray-600);
    border-color: var(--gray-300);
    background: white;
    padding: 8px 12px;
}

.btn-outline-secondary:hover {
    background-color: var(--gray-50);
    border-color: var(--gray-400);
    color: var(--gray-700);
}

.btn-light {
    background-color: var(--gray-100);
    border-color: var(--gray-200);
    color: var(--gray-700);
}

.btn-light:hover {
    background-color: var(--gray-200);
    border-color: var(--gray-300);
}

/* Table */
.income-table {
    font-size: 14px;
}

.income-table thead {
    background-color: var(--gray-50);
    border-bottom: 2px solid var(--gray-200);
}

.income-table thead th {
    font-weight: 600;
    color: var(--gray-700);
    padding: 12px 16px;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.income-table tbody td {
    padding: 16px;
    vertical-align: middle;
    border-bottom: 1px solid var(--gray-100);
}

.income-table tbody tr:hover {
    background-color: var(--gray-50);
}

/* Row Number */
.row-number {
    color: var(--gray-500);
    font-weight: 500;
    font-size: 13px;
}

/* Category */
.category-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    background-color: var(--gray-100);
    color: var(--gray-700);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 16px;
}

.category-name {
    font-weight: 500;
    color: var(--gray-900);
}

/* Amount */
.amount-text {
    font-weight: 600;
    font-size: 15px;
}

.amount-text.text-success {
    color: var(--success-color) !important;
}

/* Payment Method Badge */
.badge-method {
    background-color: var(--gray-100);
    color: var(--gray-700);
    padding: 4px 10px;
    border-radius: 6px;
    font-weight: 500;
    font-size: 12px;
    display: inline-block;
}

/* Date */
.date-text {
    color: var(--gray-600);
    font-size: 13px;
}

.date-text i {
    color: var(--gray-400);
}

/* Description */
.description-text {
    color: var(--gray-700);
    font-size: 13px;
}

.description-empty {
    font-size: 13px;
    font-style: italic;
}

/* Action Buttons */
.action-buttons {
    display: flex;
    gap: 6px;
}

.action-buttons .btn-sm {
    padding: 6px 12px;
    font-size: 13px;
}

/* Empty State */
.empty-state {
    color: var(--gray-400);
}

.empty-state i {
    opacity: 0.3;
}

.empty-state p {
    font-size: 16px;
    font-weight: 500;
    color: var(--gray-600);
}

.empty-state small {
    color: var(--gray-500);
}

/* Alerts */
.alert {
    border-radius: var(--border-radius);
    border: none;
    padding: 12px 16px;
}

.alert-success {
    background-color: #e8f5e0;
    color: #3d7a1f;
}

.alert-danger {
    background-color: #fee2e2;
    color: #991b1b;
}

/* Pagination */
.pagination {
    margin: 0;
}

.pagination .page-link {
    border-radius: 6px;
    margin: 0 3px;
    border: 1px solid var(--gray-300);
    color: var(--gray-600);
    padding: 6px 12px;
    font-size: 14px;
}

.pagination .page-link:hover {
    background-color: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
}

.pagination .page-item.active .page-link {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
}

.pagination .page-item.disabled .page-link {
    color: var(--gray-400);
    background-color: var(--gray-50);
}

/* Badge Override */
.badge.bg-light {
    background-color: var(--gray-100) !important;
    color: var(--gray-700);
    padding: 4px 12px;
    font-weight: 500;
}

/* Responsive Design */
@media (max-width: 768px) {
    .summary-card {
        padding: 16px;
    }

    .summary-icon {
        width: 48px;
        height: 48px;
    }

    .summary-icon i {
        font-size: 20px;
    }

    .summary-value {
        font-size: 18px;
    }

    .category-icon {
        width: 32px;
        height: 32px;
        font-size: 14px;
    }
    
    .action-buttons .btn-sm {
        padding: 6px 10px;
        font-size: 12px;
    }
    
    .income-table {
        font-size: 13px;
    }

    .income-table thead th,
    .income-table tbody td {
        padding: 10px;
    }
}
</style>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Allan\smart_accountant2\resources\views/income/index.blade.php ENDPATH**/ ?>