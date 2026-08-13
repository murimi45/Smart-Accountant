<?php $__env->startSection('main'); ?>
<div class="main-wrapper">
    <h4 class="mb-3">Report cards</h4>
    <?php if(session('success')): ?><div class="alert alert-success"><?php echo e(session('success')); ?></div><?php endif; ?>
    <?php if(session('error')): ?><div class="alert alert-danger"><?php echo e(session('error')); ?></div><?php endif; ?>

    <?php if (\Illuminate\Support\Facades\Blade::check('role', 'admin')): ?>
    <div class="card mb-4"><div class="card-body">
        <form method="POST" action="<?php echo e(route('grading.report-cards.store')); ?>" class="row g-3 align-items-end">
            <?php echo csrf_field(); ?>
            <div class="col-md-5">
                <label class="form-label">Enrollment</label>
                <select name="student_enrollment_id" class="form-select" required>
                    <?php $__currentLoopData = $enrollments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $enrollment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($enrollment->id); ?>"><?php echo e($enrollment->student?->full_name); ?> (<?php echo e($enrollment->id); ?>)</option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Term</label>
                <select name="term_id" class="form-select" required>
                    <?php $__currentLoopData = $terms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $term): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($term->id); ?>"><?php echo e($term->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-3"><button class="btn btn-primary w-100">Create draft</button></div>
        </form>
    </div></div>
    <?php endif; ?>

    <div class="card"><div class="card-body p-0">
        <table class="table mb-0">
            <thead><tr><th>Student</th><th>Term</th><th>Status</th><th>Published</th><th></th></tr></thead>
            <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $cards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($card->enrollment?->student?->full_name); ?></td>
                    <td><?php echo e($card->term?->name); ?></td>
                    <td><?php echo e($card->status); ?></td>
                    <td><?php echo e($card->published_at?->format('Y-m-d H:i') ?? '—'); ?></td>
                    <td class="text-end">
                        <?php if (\Illuminate\Support\Facades\Blade::check('role', 'admin')): ?>
                            <?php if(!$card->isPublished()): ?>
                                <form method="POST" action="<?php echo e(route('grading.report-cards.publish', $card)); ?>" class="d-inline"><?php echo csrf_field(); ?>
                                    <button class="btn btn-sm btn-success">Publish</button>
                                </form>
                            <?php else: ?>
                                <form method="POST" action="<?php echo e(route('grading.report-cards.unpublish', $card)); ?>" class="d-inline"><?php echo csrf_field(); ?>
                                    <button class="btn btn-sm btn-warning">Unpublish</button>
                                </form>
                                <a href="<?php echo e(route('grading.report-cards.download', $card)); ?>" class="btn btn-sm btn-primary">PDF</a>
                            <?php endif; ?>
                        <?php endif; ?>
                        <?php if (\Illuminate\Support\Facades\Blade::check('role', 'teacher')): ?>
                            <?php if($card->isPublished()): ?>
                                <a href="<?php echo e(route('grading.report-cards.download', $card)); ?>" class="btn btn-sm btn-primary">PDF</a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="5" class="text-center py-4">No report cards.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div></div>
    <div class="mt-3"><?php echo e($cards->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Allan\smart_accountant2\app\Modules\Grading\Providers/../Resources/views/report-cards/index.blade.php ENDPATH**/ ?>