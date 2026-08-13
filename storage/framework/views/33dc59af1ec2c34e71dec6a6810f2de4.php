<?php $__env->startSection('main'); ?>
<div class="main-wrapper">
    <h4 class="mb-3">Assessment types</h4>
    <?php if(session('success')): ?><div class="alert alert-success"><?php echo e(session('success')); ?></div><?php endif; ?>
    <div class="card mb-4"><div class="card-body">
        <form method="POST" action="<?php echo e(route('grading.assessment-types.store')); ?>" class="row g-3 align-items-end">
            <?php echo csrf_field(); ?>
            <div class="col-md-4"><label class="form-label">Name</label><input name="name" class="form-control" required></div>
            <div class="col-md-3"><label class="form-label">Slug</label><input name="slug" class="form-control" placeholder="auto"></div>
            <div class="col-md-3"><label class="form-check-label"><input type="checkbox" name="is_competency" value="1" class="form-check-input"> Competency</label></div>
            <div class="col-md-2"><button class="btn btn-primary w-100">Add</button></div>
        </form>
    </div></div>
    <div class="card"><div class="card-body p-0">
        <table class="table mb-0">
            <thead><tr><th>Name</th><th>Slug</th><th>Competency</th><th></th></tr></thead>
            <tbody>
            <?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($type->name); ?></td>
                    <td><?php echo e($type->slug); ?></td>
                    <td><?php echo e($type->is_competency ? 'Yes' : 'No'); ?></td>
                    <td>
                        <form method="POST" action="<?php echo e(route('grading.assessment-types.destroy', $type)); ?>"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div></div>
    <div class="mt-3"><?php echo e($types->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Allan\smart_accountant2\app\Modules\Grading\Providers/../Resources/views/assessment-types/index.blade.php ENDPATH**/ ?>