

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
                    <h5 class="mb-0"><?php echo e($label); ?></h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
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
                                            <a href="<?php echo e(route('ledger.index', ['account_id' => $account->id])); ?>">
                                                <?php echo e($account->name); ?>

                                            </a>
                                        </td>
                                        <td><?php echo e(ucfirst($account->normal_balance)); ?></td>
                                        <td>
                                            <?php if($account->is_default): ?>
                                                <span class="badge bg-secondary">System default</span>
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Allan\smart_accountant2\resources\views/accounts/index.blade.php ENDPATH**/ ?>