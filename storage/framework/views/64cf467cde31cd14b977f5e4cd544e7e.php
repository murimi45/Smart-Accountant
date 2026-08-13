<?php $__env->startSection('main'); ?>
<div class="main-wrapper">
    <h4 class="mb-3"><?php echo e($assessment ? 'Edit assessment' : 'Create assessment'); ?></h4>
    <div class="card"><div class="card-body">
        <form method="POST" action="<?php echo e($assessment ? route('grading.assessments.update', $assessment) : route('grading.assessments.store')); ?>" class="row g-3">
            <?php echo csrf_field(); ?>
            <?php if($assessment): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>
            <?php if (! ($assessment)): ?>
            <div class="col-md-3">
                <label class="form-label">Class</label>
                <select name="class_id" class="form-select" required><?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($c->id); ?>"><?php echo e($c->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Stream</label>
                <select name="stream_id" class="form-select"><option value="">All</option><?php $__currentLoopData = $streams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($s->id); ?>"><?php echo e($s->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Subject</label>
                <select name="subject_id" class="form-select" required><?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($s->id); ?>"><?php echo e($s->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Term</label>
                <select name="term_id" class="form-select" required><?php $__currentLoopData = $terms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($t->id); ?>"><?php echo e($t->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Type</label>
                <select name="assessment_type_id" class="form-select" required><?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($t->id); ?>"><?php echo e($t->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select>
            </div>
            <?php endif; ?>
            <div class="col-md-4">
                <label class="form-label">Title</label>
                <input name="title" class="form-control" value="<?php echo e(old('title', $assessment->title ?? '')); ?>" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Date</label>
                <input type="date" name="assessed_on" class="form-control" value="<?php echo e(old('assessed_on', optional($assessment->assessed_on ?? null)->format('Y-m-d'))); ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">Max score</label>
                <input type="number" step="0.01" name="max_score" class="form-control" value="<?php echo e(old('max_score', $assessment->max_score ?? '')); ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select" required>
                    <?php $__currentLoopData = ['draft','open','closed']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if(!$assessment && $status === 'closed'): ?> <?php continue; ?> <?php endif; ?>
                        <option value="<?php echo e($status); ?>" <?php if(old('status', $assessment->status ?? 'draft') === $status): echo 'selected'; endif; ?>><?php echo e(ucfirst($status)); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-12">
                <button class="btn btn-primary">Save</button>
                <a href="<?php echo e(route('grading.assessments.index')); ?>" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Allan\smart_accountant2\app\Modules\Grading\Providers/../Resources/views/assessments/form.blade.php ENDPATH**/ ?>