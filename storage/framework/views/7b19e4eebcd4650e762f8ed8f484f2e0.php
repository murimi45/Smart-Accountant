<?php $__env->startSection('main'); ?>
<div class="main-wrapper">
    <h4 class="mb-3"><?php echo e($subject ? 'Edit subject' : 'Add subject'); ?></h4>
    <div class="card"><div class="card-body">
        <form method="POST" action="<?php echo e($subject ? route('grading.subjects.update', $subject) : route('grading.subjects.store')); ?>">
            <?php echo csrf_field(); ?>
            <?php if($subject): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Name</label>
                    <input name="name" class="form-control" value="<?php echo e(old('name', $subject->name ?? '')); ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Code</label>
                    <input name="code" class="form-control" value="<?php echo e(old('code', $subject->code ?? '')); ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Parent learning area</label>
                    <select name="parent_subject_id" class="form-select">
                        <option value="">—</option>
                        <?php $__currentLoopData = $parents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $parent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($parent->id); ?>" <?php if(old('parent_subject_id', $subject->parent_subject_id ?? '') == $parent->id): echo 'selected'; endif; ?>><?php echo e($parent->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-12">
                    <label class="form-check-label">
                        <input type="checkbox" name="is_learning_area" value="1" class="form-check-input" <?php if(old('is_learning_area', $subject->is_learning_area ?? false)): echo 'checked'; endif; ?>>
                        Learning area (CBC)
                    </label>
                </div>
            </div>
            <div class="mt-3">
                <button class="btn btn-primary">Save</button>
                <a href="<?php echo e(route('grading.subjects.index')); ?>" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Allan\smart_accountant2\app\Modules\Grading\Providers/../Resources/views/subjects/form.blade.php ENDPATH**/ ?>