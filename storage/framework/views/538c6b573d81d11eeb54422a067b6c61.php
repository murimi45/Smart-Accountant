<?php $__env->startSection('main'); ?>
<div class="main-wrapper">
    <h4 class="mb-3">Link subject to class</h4>
    <div class="card"><div class="card-body">
        <form method="POST" action="<?php echo e(route('grading.class-subjects.store')); ?>" class="row g-3">
            <?php echo csrf_field(); ?>
            <div class="col-md-3">
                <label class="form-label">Class</label>
                <select name="class_id" class="form-select" required>
                    <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($class->id); ?>"><?php echo e($class->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Subject</label>
                <select name="subject_id" class="form-select" required>
                    <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($subject->id); ?>"><?php echo e($subject->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Academic year</label>
                <select name="academic_year_id" class="form-select" required>
                    <?php $__currentLoopData = $years; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($year->id); ?>"><?php echo e($year->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Term (optional)</label>
                <select name="term_id" class="form-select">
                    <option value="">Whole year</option>
                    <?php $__currentLoopData = $terms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $term): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($term->id); ?>"><?php echo e($term->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-12">
                <button class="btn btn-primary">Save</button>
                <a href="<?php echo e(route('grading.class-subjects.index')); ?>" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Allan\smart_accountant2\app\Modules\Grading\Providers/../Resources/views/class-subjects/form.blade.php ENDPATH**/ ?>