<?php $__env->startSection('main'); ?>
<div class="main-wrapper">
    <div class="d-flex justify-content-between mb-3">
        <h4>Class subjects</h4>
        <a href="<?php echo e(route('grading.class-subjects.create')); ?>" class="btn btn-success">Link subject</a>
    </div>
    <?php if(session('success')): ?><div class="alert alert-success"><?php echo e(session('success')); ?></div><?php endif; ?>
    <div class="card"><div class="card-body p-0">
        <table class="table mb-0">
            <thead><tr><th>Class</th><th>Subject</th><th>Year</th><th>Term</th><th></th></tr></thead>
            <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($item->schoolClass?->name); ?></td>
                    <td><?php echo e($item->subject?->name); ?></td>
                    <td><?php echo e($item->academicYear?->name); ?></td>
                    <td><?php echo e($item->term?->name ?? 'Whole year'); ?></td>
                    <td>
                        <form method="POST" action="<?php echo e(route('grading.class-subjects.destroy', $item)); ?>"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Remove?')">Remove</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="5" class="text-center py-4">None yet.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div></div>
    <div class="mt-3"><?php echo e($items->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Allan\smart_accountant2\app\Modules\Grading\Providers/../Resources/views/class-subjects/index.blade.php ENDPATH**/ ?>