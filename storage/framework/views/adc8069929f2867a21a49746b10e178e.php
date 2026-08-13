<?php $__env->startSection('main'); ?>
<div class="main-wrapper">
    <h4 class="mb-3">Mark entry</h4>
    <p class="text-muted">Open assessments available for entry.</p>
    <div class="card"><div class="card-body p-0">
        <table class="table mb-0">
            <thead><tr><th>Title</th><th>Class</th><th>Subject</th><th>Term</th><th></th></tr></thead>
            <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $assessments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assessment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($assessment->title); ?></td>
                    <td><?php echo e($assessment->schoolClass?->name); ?></td>
                    <td><?php echo e($assessment->subject?->name); ?></td>
                    <td><?php echo e($assessment->term?->name); ?></td>
                    <td><a href="<?php echo e(route('grading.mark-entry.show', $assessment)); ?>" class="btn btn-sm btn-primary">Open grid</a></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="5" class="text-center py-4">No open assessments.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Allan\smart_accountant2\app\Modules\Grading\Providers/../Resources/views/mark-entry/index.blade.php ENDPATH**/ ?>