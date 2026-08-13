<?php $__env->startSection('main'); ?>
<div class="main-wrapper">
    <h4 class="mb-3">Assessment weights</h4>
    <?php if(session('success')): ?><div class="alert alert-success"><?php echo e(session('success')); ?></div><?php endif; ?>
    <div class="card mb-4"><div class="card-body">
        <form method="POST" action="<?php echo e(route('grading.weights.store')); ?>" class="row g-3">
            <?php echo csrf_field(); ?>
            <div class="col-md-2"><label class="form-label">Scheme</label><select name="grading_scheme_id" class="form-select" required><?php $__currentLoopData = $schemes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($s->id); ?>"><?php echo e($s->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
            <div class="col-md-2"><label class="form-label">Class</label><select name="class_id" class="form-select" required><?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($c->id); ?>"><?php echo e($c->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
            <div class="col-md-2"><label class="form-label">Subject</label><select name="subject_id" class="form-select" required><?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($s->id); ?>"><?php echo e($s->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
            <div class="col-md-2"><label class="form-label">Term</label><select name="term_id" class="form-select" required><?php $__currentLoopData = $terms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($t->id); ?>"><?php echo e($t->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
            <div class="col-md-2"><label class="form-label">Type</label><select name="assessment_type_id" class="form-select" required><?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($t->id); ?>"><?php echo e($t->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
            <div class="col-md-1"><label class="form-label">%</label><input type="number" step="0.01" name="weight_percent" class="form-control" required></div>
            <div class="col-md-1 d-flex align-items-end"><button class="btn btn-primary w-100">Save</button></div>
        </form>
    </div></div>
    <div class="card"><div class="card-body p-0">
        <table class="table mb-0">
            <thead><tr><th>Class</th><th>Subject</th><th>Term</th><th>Type</th><th>%</th><th></th></tr></thead>
            <tbody>
            <?php $__currentLoopData = $weights; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $weight): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($weight->schoolClass?->name); ?></td>
                    <td><?php echo e($weight->subject?->name); ?></td>
                    <td><?php echo e($weight->term?->name); ?></td>
                    <td><?php echo e($weight->assessmentType?->name); ?></td>
                    <td><?php echo e($weight->weight_percent); ?></td>
                    <td>
                        <form method="POST" action="<?php echo e(route('grading.weights.destroy', $weight)); ?>"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            <button class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div></div>
    <div class="mt-3"><?php echo e($weights->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Allan\smart_accountant2\app\Modules\Grading\Providers/../Resources/views/weights/index.blade.php ENDPATH**/ ?>