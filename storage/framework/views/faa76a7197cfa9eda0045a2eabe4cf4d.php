<?php $__env->startSection('main'); ?>
<div class="main-wrapper">
    <div class="d-flex justify-content-between mb-3">
        <h4>Assessments</h4>
        <a href="<?php echo e(route('grading.assessments.create')); ?>" class="btn btn-success">Create</a>
    </div>
    <?php if(session('success')): ?><div class="alert alert-success"><?php echo e(session('success')); ?></div><?php endif; ?>
    <div class="card"><div class="card-body p-0">
        <table class="table mb-0">
            <thead><tr><th>Title</th><th>Class</th><th>Subject</th><th>Term</th><th>Type</th><th>Status</th><th></th></tr></thead>
            <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $assessments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assessment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($assessment->title); ?></td>
                    <td><?php echo e($assessment->schoolClass?->name); ?></td>
                    <td><?php echo e($assessment->subject?->name); ?></td>
                    <td><?php echo e($assessment->term?->name); ?></td>
                    <td><?php echo e($assessment->assessmentType?->name); ?></td>
                    <td><?php echo e($assessment->status); ?></td>
                    <td class="text-end">
                        <?php if($assessment->isOpen()): ?>
                            <a href="<?php echo e(route('grading.mark-entry.show', $assessment)); ?>" class="btn btn-sm btn-primary">Enter</a>
                        <?php endif; ?>
                        <a href="<?php echo e(route('grading.assessments.edit', $assessment)); ?>" class="btn btn-sm btn-light">Edit</a>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="7" class="text-center py-4">No assessments.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div></div>
    <div class="mt-3"><?php echo e($assessments->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Allan\smart_accountant2\app\Modules\Grading\Providers/../Resources/views/assessments/index.blade.php ENDPATH**/ ?>