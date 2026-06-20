

<?php $__env->startSection('main'); ?>
<div class="main-wrapper">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h4 class="mb-1">General Ledger</h4>
                <p class="text-muted mb-0">Double-entry lines posted automatically from cashbook transactions</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="<?php echo e(route('accounts.index')); ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="fa fa-list me-1"></i>Chart of Accounts
                </a>
            </div>
        </div>
    </div>

    <div class="card filter-card mb-4">
        <div class="card-body">
            <form method="GET" action="<?php echo e(route('ledger.index')); ?>" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Account</label>
                    <select name="account_id" class="form-select">
                        <option value="">All accounts</option>
                        <?php $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($account->id); ?>" <?php echo e((string) $accountId === (string) $account->id ? 'selected' : ''); ?>>
                                <?php echo e($account->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">From</label>
                    <input type="date" name="date_from" class="form-control" value="<?php echo e($dateFrom); ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">To</label>
                    <input type="date" name="date_to" class="form-control" value="<?php echo e($dateTo); ?>">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary me-2">Filter</button>
                    <a href="<?php echo e(route('ledger.index')); ?>" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <div class="summary-card-secondary">
                <div class="summary-content-secondary">
                    <div class="summary-value-secondary">KSh <?php echo e(number_format($totalDebit, 2)); ?></div>
                    <div class="summary-label-secondary">Total Debits</div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="summary-card-secondary">
                <div class="summary-content-secondary">
                    <div class="summary-value-secondary">KSh <?php echo e(number_format($totalCredit, 2)); ?></div>
                    <div class="summary-label-secondary">Total Credits</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card table-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fa fa-book me-2"></i>Ledger Entries</h5>
            <span class="badge bg-light text-dark"><?php echo e($entries->total()); ?> lines</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Account</th>
                            <th>Description</th>
                            <th class="text-end">Debit</th>
                            <th class="text-end">Credit</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $entries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($entry->transaction_date?->format('d M Y')); ?></td>
                                <td><?php echo e($entry->account?->name); ?></td>
                                <td><?php echo e($entry->description); ?></td>
                                <td class="text-end">
                                    <?php if((float) $entry->debit > 0): ?>
                                        <?php echo e(number_format($entry->debit, 2)); ?>

                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <?php if((float) $entry->credit > 0): ?>
                                        <?php echo e(number_format($entry->credit, 2)); ?>

                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    No ledger entries yet. Postings are created when cashbook transactions are recorded.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php if($entries->hasPages()): ?>
            <div class="card-footer">
                <?php echo e($entries->links()); ?>

            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Allan\smart_accountant2\resources\views/ledger/index.blade.php ENDPATH**/ ?>