

<?php $__env->startSection('main'); ?>
<div class="main-wrapper">
    
    <div class="page-header mb-4">
        <h4 class="mb-1">Chart of Accounts</h4>
        <p class="text-muted mb-0">System default accounts used for general ledger postings from cashbook activity</p>
    </div>

    <?php $__currentLoopData = $categoryLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if(isset($accounts[$key]) && $accounts[$key]->isNotEmpty()): ?>
            <div class="card table-card mb-4">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fa fa-folder-open me-2"></i><?php echo e($label); ?>

                        </h5>
                        <span class="badge bg-light text-dark"><?php echo e($accounts[$key]->count()); ?> Accounts</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table admin-table mb-0">
                            <thead>
                                <tr>
                                    <th>Account Name</th>
                                    <th>Normal Balance</th>
                                    <th>Type</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $accounts[$key]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td>
                                            <a href="<?php echo e(route('ledger.index', ['account_id' => $account->id])); ?>" class="account-link">
                                                <?php echo e($account->name); ?>

                                            </a>
                                        </td>
                                        <td>
                                            <span class="role-badge"><?php echo e(ucfirst($account->normal_balance)); ?></span>
                                        </td>
                                        <td>
                                            <?php if($account->is_default): ?>
                                                <span class="default-badge">
                                                    <i class="fa fa-lock me-1"></i>System default
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>

<style>
/* Base Variables — matched to Admin Management page */
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
.table-card {
    border: 1px solid var(--gray-200);
    border-radius: var(--border-radius);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.table-card .card-header {
    background: var(--gray-50);
    border-bottom: 1px solid var(--gray-200);
    padding: 16px 20px;
}

.table-card .card-header h5 {
    font-size: 16px;
    font-weight: 600;
    color: var(--gray-900);
}

/* Table */
.admin-table {
    font-size: 14px;
}

.admin-table thead {
    background-color: var(--gray-50);
    border-bottom: 2px solid var(--gray-200);
}

.admin-table thead th {
    font-weight: 600;
    color: var(--gray-700);
    padding: 12px 16px;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.admin-table tbody td {
    padding: 16px;
    vertical-align: middle;
    border-bottom: 1px solid var(--gray-100);
}

.admin-table tbody tr:hover {
    background-color: var(--gray-50);
}

/* Account Link */
.account-link {
    color: var(--gray-900);
    font-weight: 500;
    text-decoration: none;
}

.account-link:hover {
    color: var(--primary-color);
    text-decoration: underline;
}

/* Role Badge (reused for Normal Balance) */
.role-badge {
    background-color: var(--gray-100);
    color: var(--gray-700);
    padding: 4px 10px;
    border-radius: 6px;
    font-weight: 500;
    font-size: 12px;
    display: inline-block;
}

/* Default Badge */
.default-badge {
    background-color: rgba(54, 169, 226, 0.1);
    color: #2a8cbd;
    padding: 4px 10px;
    border-radius: 6px;
    font-weight: 500;
    font-size: 12px;
    display: inline-flex;
    align-items: center;
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
    .admin-table {
        font-size: 13px;
    }

    .admin-table thead th,
    .admin-table tbody td {
        padding: 10px;
    }
}
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Allan\smart_accountant2\resources\views/accounts/index.blade.php ENDPATH**/ ?>