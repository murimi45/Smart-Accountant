

<?php $__env->startSection('main'); ?>
<div class="main-wrapper">
    
    <div class="page-header mb-4">
        <h4 class="mb-1">SMS Logs</h4>
        <p class="text-muted mb-0">View SMS communication history and status</p>
    </div>

    
    <div class="card table-card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fa fa-sms me-2"></i>SMS History
                </h5>
                <span class="badge bg-light text-dark"><?php echo e($logs->total()); ?> Messages</span>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table sms-table mb-0">
                    <thead>
                        <tr>
                            <th style="width: 60px;">ID</th>
                            <th>Recipient</th>
                            <th>Message</th>
                            <th>Status</th>
                            <th>Response</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <span class="row-number"><?php echo e($log->id); ?></span>
                            </td>
                            <td>
                                <span class="phone-number"><?php echo e($log->to); ?></span>
                            </td>
                            <td>
                                <span class="message-text"><?php echo e(Str::limit($log->message, 80)); ?></span>
                            </td>
                            <td>
                                <?php if($log->status === 'success'): ?>
                                    <span class="badge badge-success">
                                        <i class="fa fa-check me-1"></i>Success
                                    </span>
                                <?php elseif($log->status === 'failed'): ?>
                                    <span class="badge badge-danger">
                                        <i class="fa fa-times me-1"></i>Failed
                                    </span>
                                <?php else: ?>
                                    <span class="badge badge-warning">
                                        <i class="fa fa-clock me-1"></i><?php echo e(ucfirst($log->status)); ?>

                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($log->response): ?>
                                    <div class="response-box"><?php echo e(Str::limit($log->response, 100)); ?></div>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="date-text">
                                    <i class="fa fa-calendar-day me-1"></i>
                                    <?php echo e($log->created_at->format('d M, Y H:i')); ?>

                                </span>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fa fa-inbox fa-3x mb-3"></i>
                                    <p class="mb-0">No SMS logs found</p>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        
        <?php if($logs->hasPages()): ?>
        <div class="card-footer">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div class="mb-2 mb-sm-0">
                    <small class="text-muted">
                        Showing <?php echo e($logs->firstItem()); ?> to <?php echo e($logs->lastItem()); ?> of <?php echo e($logs->total()); ?> entries
                    </small>
                </div>
                <div>
                    <?php echo e($logs->links()); ?>

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
.sms-table {
    font-size: 14px;
}

.sms-table thead {
    background-color: var(--gray-50);
    border-bottom: 2px solid var(--gray-200);
}

.sms-table thead th {
    font-weight: 600;
    color: var(--gray-700);
    padding: 12px 16px;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.sms-table tbody td {
    padding: 16px;
    vertical-align: middle;
    border-bottom: 1px solid var(--gray-100);
}

.sms-table tbody tr:hover {
    background-color: var(--gray-50);
}

/* Row Number */
.row-number {
    color: var(--gray-500);
    font-weight: 500;
    font-size: 13px;
}

/* Phone Number */
.phone-number {
    font-weight: 500;
    color: var(--gray-900);
    font-family: monospace;
}

/* Message */
.message-text {
    color: var(--gray-700);
    font-size: 13px;
}

/* Status Badges */
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

.badge-danger {
    background-color: #fee2e2;
    color: #991b1b;
}

.badge-warning {
    background-color: #fef3c7;
    color: #92400e;
}

/* Response Box */
.response-box {
    max-height: 60px;
    overflow: auto;
    background: var(--gray-50);
    padding: 6px 10px;
    border-radius: 4px;
    font-size: 12px;
    color: var(--gray-700);
    font-family: monospace;
    white-space: pre-wrap;
    word-break: break-word;
}

.response-box::-webkit-scrollbar {
    width: 4px;
    height: 4px;
}

.response-box::-webkit-scrollbar-thumb {
    background: var(--gray-300);
    border-radius: 2px;
}

/* Date */
.date-text {
    color: var(--gray-600);
    font-size: 13px;
}

.date-text i {
    color: var(--gray-400);
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

/* Badge Override */
.badge.bg-light {
    background-color: var(--gray-100) !important;
    color: var(--gray-700);
    padding: 4px 12px;
    font-weight: 500;
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

/* Responsive Design */
@media (max-width: 768px) {
    .sms-table {
        font-size: 13px;
    }

    .sms-table thead th,
    .sms-table tbody td {
        padding: 10px;
    }

    .message-text {
        font-size: 12px;
    }

    .response-box {
        font-size: 11px;
    }
}
</style>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Allan\smart_accountant2\resources\views/sms/logs.blade.php ENDPATH**/ ?>