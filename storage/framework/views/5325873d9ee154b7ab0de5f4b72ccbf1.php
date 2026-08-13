<?php $__env->startSection('main'); ?>
<div class="main-wrapper">
    <div class="d-flex justify-content-between mb-3">
        <h4>Subjects / learning areas</h4>
        <a href="<?php echo e(route('grading.subjects.create')); ?>" class="btn btn-success">Add</a>
    </div>
    <?php if(session('success')): ?><div class="alert alert-success"><?php echo e(session('success')); ?></div><?php endif; ?>
    <div class="card"><div class="card-body p-0">
        <table class="table mb-0">
            <thead><tr><th>Name</th><th>Code</th><th>Learning area</th><th>Parent</th><th></th></tr></thead>
            <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($subject->name); ?></td>
                    <td><?php echo e($subject->code); ?></td>
                    <td><?php echo e($subject->is_learning_area ? 'Yes' : 'No'); ?></td>
                    <td><?php echo e($subject->parent?->name); ?></td>
                    <td class="text-end">
                        <a href="<?php echo e(route('grading.subjects.edit', $subject)); ?>" class="btn btn-sm btn-light">Edit</a>
                        <form action="<?php echo e(route('grading.subjects.destroy', $subject)); ?>" method="POST" class="d-inline"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="5" class="text-center py-4">No subjects yet.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div></div>
    <div class="mt-3"><?php echo e($subjects->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Allan\smart_accountant2\app\Modules\Grading\Providers/../Resources/views/subjects/index.blade.php ENDPATH**/ ?>