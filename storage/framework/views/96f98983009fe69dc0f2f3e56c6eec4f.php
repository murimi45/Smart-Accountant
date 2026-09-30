<?php $__env->startSection('main'); ?>
<div class="main-wrapper">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h4 class="mb-1"><?php echo e($run->period->format('F Y')); ?></h4>
                <p class="text-muted mb-0"><?php echo e(ucfirst($run->status)); ?></p>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="<?php echo e(route('payroll.runs.index')); ?>" class="btn btn-secondary">Back</a>
            </div>
        </div>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="alert alert-danger"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <div class="row g-3 mb-4">
        <div class="col"><div class="card"><div class="card-body"><div class="text-muted small">Headcount</div><strong><?php echo e($totals['headcount']); ?></strong></div></div></div>
        <div class="col"><div class="card"><div class="card-body"><div class="text-muted small">Gross</div><strong>KES <?php echo e(number_format($totals['gross'], 2)); ?></strong></div></div></div>
        <div class="col"><div class="card"><div class="card-body"><div class="text-muted small">Deductions</div><strong>KES <?php echo e(number_format($totals['deductions'], 2)); ?></strong></div></div></div>
        <div class="col"><div class="card"><div class="card-body"><div class="text-muted small">Net pay</div><strong>KES <?php echo e(number_format($totals['net_pay'], 2)); ?></strong></div></div></div>
        <div class="col"><div class="card"><div class="card-body"><div class="text-muted small">Employer cost</div><strong>KES <?php echo e(number_format($totals['employer_cost'], 2)); ?></strong></div></div></div>
    </div>

    <?php if (! ($run->isLocked())): ?>
        <form action="<?php echo e(route('payroll.runs.recalculate', $run->id)); ?>" method="POST" class="d-inline">
            <?php echo csrf_field(); ?>
            <button class="btn btn-outline-secondary mb-3">Recalculate</button>
        </form>
        <form action="<?php echo e(route('payroll.runs.lock', $run->id)); ?>" method="POST" class="d-inline">
            <?php echo csrf_field(); ?>
            <button class="btn btn-success mb-3" onclick="return confirm('Lock this month? Payslips cannot be changed after this.')">Lock month</button>
        </form>
    <?php endif; ?>

    <div class="card">
        <div class="card-body p-0">
            <table class="table mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Staff</th>
                        <th>Gross</th>
                        <th>PAYE</th>
                        <th>Net</th>
                        <th>Lines</th>
                        <?php if (! ($run->isLocked())): ?>
                            <th>This month</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $payslips; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payslip): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="<?php echo \Illuminate\Support\Arr::toCssClasses(['text-muted' => ! $payslip->included]); ?>">
                            <td>
                                <?php echo e($payslip->full_name); ?>

                                <div class="small text-muted"><?php echo e($payslip->staff_number); ?></div>
                            </td>
                            <td><?php echo e(number_format($payslip->gross, 2)); ?></td>
                            <td><?php echo e(number_format($payslip->paye, 2)); ?></td>
                            <td><?php echo e(number_format($payslip->net_pay, 2)); ?></td>
                            <td>
                                <details>
                                    <summary>View</summary>
                                    <div class="small mt-2">
                                        <div>Taxable pay: KES <?php echo e(number_format($payslip->taxable_pay, 2)); ?></div>
                                        <?php $__currentLoopData = $payslip->lines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div><?php echo e($line->name); ?>: KES <?php echo e(number_format($line->amount, 2)); ?></div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </details>
                            </td>
                            <?php if (! ($run->isLocked())): ?>
                                <td>
                                    <form action="<?php echo e(route('payroll.payslips.update', [$run->id, $payslip->id])); ?>" method="POST" class="d-flex gap-2 align-items-center">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PUT'); ?>
                                        <input type="hidden" name="included" value="0">
                                        <label class="small mb-0">
                                            <input type="checkbox" name="included" value="1" <?php if($payslip->included): echo 'checked'; endif; ?>> Include
                                        </label>
                                        <input type="number" name="adjustment" class="form-control form-control-sm" step="0.01" value="<?php echo e($payslip->adjustment); ?>" style="width: 110px" title="One-off amount added to gross">
                                        <button class="btn btn-sm btn-success">Save</button>
                                    </form>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Allan\smart_accountant2\resources\views/payroll/runs/show.blade.php ENDPATH**/ ?>