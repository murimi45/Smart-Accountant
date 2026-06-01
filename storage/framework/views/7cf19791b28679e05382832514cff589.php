

<?php $__env->startSection('main'); ?>
<div class="main-wrapper">
    
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h4 class="mb-1">Cashbook</h4>
                <p class="text-muted mb-0">Track all cash inflows and outflows</p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <div class="balance-card">
                    <div class="balance-icon">
                        <i class="fa fa-wallet"></i>
                    </div>
                    <div class="balance-content">
                        <div class="balance-label">Current Balance</div>
                        <div class="balance-value">KSh <?php echo e(number_format($balance, 2)); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="card table-card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fa fa-book me-2"></i>Transaction History
                </h5>
                <span class="badge bg-light text-dark"><?php echo e($entries->total()); ?> Entries</span>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table cashbook-table mb-0">
                    <thead>
                        <tr>
                            <th style="width: 120px;">Date</th>
                            <th>Description</th>
                            <th>Payment Method</th>
                            <th class="text-end">Cash Inflow (KSh)</th>
                            <th class="text-end">Cash Outflow (KSh)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $totalInflow = 0;
                            $totalOutflow = 0;
                        ?>

                        <?php $__empty_1 = true; $__currentLoopData = $entries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td>
                                    <span class="date-text">
                                        <i class="fa fa-calendar-day me-1"></i>
                                        <?php echo e(\Carbon\Carbon::parse($entry->transaction_date)->format('d M, Y')); ?>

                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="transaction-icon <?php echo e($entry->transaction_type === 'inflow' ? 'transaction-inflow' : 'transaction-outflow'); ?>">
                                            <i class="fa fa-<?php echo e($entry->transaction_type === 'inflow' ? 'arrow-down' : 'arrow-up'); ?>"></i>
                                        </div>
                                        <span class="description-text"><?php echo e($entry->description); ?></span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge-method"><?php echo e(ucfirst($entry->payment_method)); ?></span>
                                </td>
                                <td class="text-end">
                                    <?php if($entry->transaction_type === 'inflow'): ?>
                                        <span class="amount-inflow">
                                            <i class="fa fa-plus-circle me-1"></i><?php echo e(number_format($entry->amount, 2)); ?>

                                        </span>
                                        <?php $totalInflow += $entry->amount; ?>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <?php if($entry->transaction_type === 'outflow'): ?>
                                        <span class="amount-outflow">
                                            <i class="fa fa-minus-circle me-1"></i><?php echo e(number_format($entry->amount, 2)); ?>

                                        </span>
                                        <?php $totalOutflow += $entry->amount; ?>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="fa fa-inbox fa-3x mb-3"></i>
                                        <p class="mb-0">No transactions found</p>
                                        <small>Transaction history will appear here</small>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>

                    <?php if($entries->count() > 0): ?>
                    <tfoot>
                        <tr class="totals-row">
                            <td colspan="3" class="text-end fw-bold">
                                <i class="fa fa-calculator me-2"></i>Totals:
                            </td>
                            <td class="text-end">
                                <span class="total-inflow">KSh <?php echo e(number_format($totalInflow, 2)); ?></span>
                            </td>
                            <td class="text-end">
                                <span class="total-outflow">KSh <?php echo e(number_format($totalOutflow, 2)); ?></span>
                            </td>
                        </tr>
                        <tr class="balance-row">
                            <td colspan="3" class="text-end fw-bold">
                                <i class="fa fa-wallet me-2"></i>Closing Balance:
                            </td>
                            <td colspan="2" class="text-end">
                                <span class="closing-balance">KSh <?php echo e(number_format($totalInflow - $totalOutflow, 2)); ?></span>
                            </td>
                        </tr>
                    </tfoot>
                    <?php endif; ?>
                </table>
            </div>
        </div>

        
        <?php if($entries->hasPages()): ?>
        <div class="card-footer">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div class="mb-2 mb-sm-0">
                    <small class="text-muted">
                        Showing <?php echo e($entries->firstItem()); ?> to <?php echo e($entries->lastItem()); ?> of <?php echo e($entries->total()); ?> entries
                    </small>
                </div>
                <div>
                    <?php echo e($entries->links('pagination::bootstrap-4')); ?>


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

/* Balance Card */
.balance-card {
    display: inline-flex;
    align-items: center;
    gap: 16px;
    background: white;
    border: 1px solid var(--gray-200);
    border-radius: var(--border-radius);
    padding: 16px 24px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.balance-icon {
    width: 50px;
    height: 50px;
    border-radius: 10px;
    background-color: #e0f2fe;
    color: var(--primary-color);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
}

.balance-label {
    font-size: 12px;
    color: var(--gray-500);
    font-weight: 500;
    margin-bottom: 4px;
}

.balance-value {
    font-size: 24px;
    font-weight: 700;
    color: var(--gray-900);
}

/* Card */
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

.card-footer {
    background: var(--gray-50);
    border-top: 1px solid var(--gray-200);
    padding: 16px 20px;
}

/* Table */
.cashbook-table {
    font-size: 14px;
}

.cashbook-table thead {
    background-color: var(--gray-50);
    border-bottom: 2px solid var(--gray-200);
}

.cashbook-table thead th {
    font-weight: 600;
    color: var(--gray-700);
    padding: 12px 16px;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.cashbook-table tbody td {
    padding: 16px;
    vertical-align: middle;
    border-bottom: 1px solid var(--gray-100);
}

.cashbook-table tbody tr:hover {
    background-color: var(--gray-50);
}

/* Date */
.date-text {
    color: var(--gray-600);
    font-size: 13px;
}

.date-text i {
    color: var(--gray-400);
}

/* Transaction Icon */
.transaction-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    flex-shrink: 0;
    margin-right: 12px;
}

.transaction-inflow {
    background-color: #e8f5e0;
    color: var(--success-color);
}

.transaction-outflow {
    background-color: #fee2e2;
    color: var(--danger-color);
}

/* Description */
.description-text {
    color: var(--gray-700);
    font-size: 13px;
}

/* Badge Method */
.badge-method {
    background-color: var(--gray-100);
    color: var(--gray-700);
    padding: 4px 10px;
    border-radius: 6px;
    font-weight: 500;
    font-size: 12px;
    display: inline-block;
}

/* Amounts */
.amount-inflow {
    color: var(--success-color);
    font-weight: 600;
    font-size: 14px;
}

.amount-outflow {
    color: var(--danger-color);
    font-weight: 600;
    font-size: 14px;
}

/* Footer */
.cashbook-table tfoot {
    background: var(--gray-50);
    border-top: 2px solid var(--gray-200);
}

.cashbook-table tfoot td {
    padding: 16px;
    font-size: 14px;
}

.totals-row td {
    color: var(--gray-700);
}

.total-inflow {
    color: var(--success-color);
    font-weight: 700;
    font-size: 15px;
}

.total-outflow {
    color: var(--danger-color);
    font-weight: 700;
    font-size: 15px;
}

.balance-row {
    background: #e0f2fe !important;
}

.closing-balance {
    color: var(--primary-color);
    font-weight: 700;
    font-size: 16px;
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

/* Badge Override */
.badge.bg-light {
    background-color: var(--gray-100) !important;
    color: var(--gray-700);
    padding: 4px 12px;
    font-weight: 500;
}

/* Pagination Styling */
.pagination {
    margin: 0;
    display: flex;
    list-style: none;
    padding: 0;
}

.pagination .page-item {
    margin: 0 2px;
}

.pagination .page-link {
    position: relative;
    display: block;
    padding: 6px 12px;
    font-size: 14px;
    font-weight: 500;
    color: var(--gray-600);
    text-decoration: none;
    background-color: white;
    border: 1px solid var(--gray-300);
    border-radius: 6px;
    transition: all 0.2s;
}

.pagination .page-link:hover {
    background-color: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
}

.pagination .page-item.active .page-link {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
    color: white;
    z-index: 1;
}

.pagination .page-item.disabled .page-link {
    color: var(--gray-400);
    background-color: var(--gray-50);
    border-color: var(--gray-200);
    cursor: not-allowed;
    pointer-events: none;
}

/* Remove default pagination symbols and style text */
.pagination .page-link svg {
    display: none;
}

.pagination .page-item:first-child .page-link::before {
    content: '← Previous';
    font-size: 13px;
}

.pagination .page-item:last-child .page-link::before {
    content: 'Next →';
    font-size: 13px;
}

.pagination .page-item:not(:first-child):not(:last-child) .page-link::before {
    content: attr(aria-label);
}

/* Hide default text in prev/next buttons */
.pagination .page-item:first-child .page-link,
.pagination .page-item:last-child .page-link {
    font-size: 0;
}

.pagination .page-item:first-child .page-link::before,
.pagination .page-item:last-child .page-link::before {
    font-size: 13px;
}

/* Responsive Design */
@media (max-width: 768px) {
    .balance-card {
        width: 100%;
        justify-content: center;
    }

    .transaction-icon {
        width: 28px;
        height: 28px;
        font-size: 11px;
    }
    
    .cashbook-table {
        font-size: 13px;
    }

    .cashbook-table thead th,
    .cashbook-table tbody td,
    .cashbook-table tfoot td {
        padding: 10px;
    }

    .description-text {
        font-size: 12px;
    }

    .pagination .page-link {
        padding: 5px 10px;
        font-size: 12px;
    }
}

@media (max-width: 576px) {
    .cashbook-table thead th:nth-child(3),
    .cashbook-table tbody td:nth-child(3),
    .cashbook-table tfoot td:nth-child(3) {
        display: none;
    }

    .pagination .page-item:first-child .page-link::before {
        content: '←';
    }

    .pagination .page-item:last-child .page-link::before {
        content: '→';
    }
}

/* Fix pagination styling */
.pagination {
    margin: 0;
    display: flex;
    gap: 4px;
}

.pagination .page-link {
    padding: 6px 12px;
    font-size: 13px;
    border-radius: 6px;
    color: var(--gray-600);
    border: 1px solid var(--gray-300);
}

.pagination .page-item.active .page-link {
    background: var(--primary-color);
    border-color: var(--primary-color);
    color: white;
}

.pagination .page-item.disabled .page-link {
    background: var(--gray-100);
    color: var(--gray-400);
    border-color: var(--gray-200);
}

.pagination .page-item:first-child .page-link::before {
    content: "← Prev";
    font-size: 13px;
}

.pagination .page-item:last-child .page-link::before {
    content: "Next →";
    font-size: 13px;
}

/* hide default arrow icons */
.pagination .page-link svg {
    display: none;
}

/* hide default text in prev/next */
.pagination .page-item:first-child .page-link,
.pagination .page-item:last-child .page-link {
    font-size: 0;
}

</style>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Allan\smart_accountant2\resources\views/cashbook/index.blade.php ENDPATH**/ ?>