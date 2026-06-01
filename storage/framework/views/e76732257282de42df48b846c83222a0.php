
<?php $__env->startSection('main'); ?>

<div class="main-wrapper">
    
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h4 class="mb-1">Extra Fee Management</h4>
                <p class="text-muted mb-0">Manage additional fees and charges</p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <a href="<?php echo e(route('addextrafee')); ?>" class="btn btn-success">
                    <i class="fa fa-plus me-2"></i>Add New Fee
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

    
    <div class="card filter-card mb-4">
        <div class="card-body">
            <form method="GET" action="<?php echo e(route('extrafeelist')); ?>" class="filter-form">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label"><i class="fa fa-calendar me-1"></i>Term</label>
                        <select name="term_id" class="form-select">
                            <option value="">All Terms</option>
                            <?php $__currentLoopData = $terms ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $term): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($term->id); ?>" <?php echo e(request('term_id') == $term->id ? 'selected' : ''); ?>>
                                    <?php echo e($term->name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label"><i class="fa fa-calendar-alt me-1"></i>Year</label>
                        <select name="year" class="form-select">
                            <option value="">All Years</option>
                            <?php $__currentLoopData = $years ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($year); ?>" <?php echo e(request('year') == $year ? 'selected' : ''); ?>>
                                    <?php echo e($year); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label"><i class="fa fa-search me-1"></i>Search Fee Name</label>
                        <input type="text" 
                               name="search" 
                               class="form-control" 
                               value="<?php echo e(request('search')); ?>"
                               placeholder="Enter fee name">
                    </div>

                    <div class="col-md-2">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="fa fa-filter me-1"></i>Filter
                            </button>
                            <a href="<?php echo e(route('extrafeelist')); ?>" class="btn btn-outline-secondary" title="Reset">
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
                    <i class="fa fa-receipt me-2"></i>Extra Fee List
                </h5>
                <span class="badge bg-light text-dark"><?php echo e(count($extraFees)); ?> Fees</span>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table fee-table mb-0">
                    <thead>
                        <tr>
                            <th>Fee Name</th>
                            <th>Amount</th>
                            <th>Quantity Based</th>
                            <th>Term</th>
                            <th>Year</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $extraFees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $extraFee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="fee-icon">
                                        <?php echo e(strtoupper(substr($extraFee->name, 0, 1))); ?>

                                    </div>
                                    <div class="ms-3">
                                        <div class="fee-name"><?php echo e($extraFee->name); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="amount-text">KSh <?php echo e(number_format($extraFee->amount, 2)); ?></span>
                            </td>
                            <td>
                                <?php if($extraFee->is_quantity_based): ?>
                                    <span class="badge badge-success">
                                        <i class="fa fa-check me-1"></i>Yes
                                    </span>
                                <?php else: ?>
                                    <span class="badge-method">No</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge-term"><?php echo e($extraFee->term->name); ?></span>
                            </td>
                            <td>
                                <span class="year-text"><?php echo e($extraFee->year); ?></span>
                            </td>
                            <td>
                                <?php if($extraFee->description): ?>
                                    <span class="description-text"><?php echo e(Str::limit($extraFee->description, 40)); ?></span>
                                <?php else: ?>
                                    <span class="text-muted description-empty">
                                        <i class="fa fa-minus me-1"></i>No description
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="<?php echo e(url('/editextrafee/' . $extraFee->id)); ?>" 
                                       class="btn btn-sm btn-light" 
                                       title="Edit Fee">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <a href="<?php echo e(url('/deleteextrafee/' . $extraFee->id)); ?>" 
                                       class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Are you sure you want to delete this extra fee?')" 
                                       title="Delete Fee">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fa fa-inbox fa-3x mb-3"></i>
                                    <p class="mb-2">No extra fees configured</p>
                                    <small class="d-block mb-3">Click "Add New Fee" to create one</small>
                                    <a href="<?php echo e(route('addextrafee')); ?>" class="btn btn-success">
                                        <i class="fa fa-plus me-2"></i>Add New Fee
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
/* Base Variables */
:root {
    --primary-color: #36a9e2;
    --success-color: #79c347;
    --success-dark: #5fa732;
    --danger-color: #ef4444;
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
.fee-table {
    font-size: 14px;
}

.fee-table thead {
    background-color: var(--gray-50);
    border-bottom: 2px solid var(--gray-200);
}

.fee-table thead th {
    font-weight: 600;
    color: var(--gray-700);
    padding: 12px 16px;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.fee-table tbody td {
    padding: 16px;
    vertical-align: middle;
    border-bottom: 1px solid var(--gray-100);
}

.fee-table tbody tr:hover {
    background-color: var(--gray-50);
}

/* Fee Icon */
.fee-icon {
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
    flex-shrink: 0;
}

.fee-name {
    font-weight: 500;
    color: var(--gray-900);
}

/* Amount */
.amount-text {
    font-weight: 600;
    color: var(--gray-900);
    font-size: 14px;
}

/* Badges */
.badge {
    padding: 4px 10px;
    border-radius: 6px;
    font-weight: 500;
    font-size: 12px;
}

.badge-success {
    background-color: #e8f5e0;
    color: #3d7a1f;
}

.badge-method {
    background-color: var(--gray-100);
    color: var(--gray-700);
    padding: 4px 10px;
    border-radius: 6px;
    font-weight: 500;
    font-size: 12px;
    display: inline-block;
}

.badge-term {
    background-color: var(--gray-100);
    color: var(--gray-700);
    padding: 4px 10px;
    border-radius: 6px;
    font-weight: 500;
    font-size: 12px;
    display: inline-block;
}

/* Year */
.year-text {
    color: var(--gray-600);
    font-weight: 500;
    font-size: 13px;
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

/* Badge Override */
.badge.bg-light {
    background-color: var(--gray-100) !important;
    color: var(--gray-700);
    padding: 4px 12px;
    font-weight: 500;
}

/* Responsive Design */
@media (max-width: 768px) {
    .fee-icon {
        width: 32px;
        height: 32px;
        font-size: 14px;
    }
    
    .action-buttons .btn-sm {
        padding: 6px 10px;
        font-size: 12px;
    }
    
    .fee-table {
        font-size: 13px;
    }

    .fee-table thead th,
    .fee-table tbody td {
        padding: 10px;
    }

    .description-text {
        font-size: 12px;
    }
}
</style>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Allan\smart_accountant2\resources\views/extrafee/list.blade.php ENDPATH**/ ?>