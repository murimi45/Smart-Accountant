<?php $__env->startSection('main'); ?>
<div class="main-wrapper">
    <div class="mb-3">
        <a href="<?php echo e(route('grading.mark-entry.index')); ?>" class="btn btn-outline-secondary btn-sm">Back</a>
    </div>
    <h4 class="mb-1"><?php echo e($assessment->title); ?></h4>
    <p class="text-muted"><?php echo e($assessment->schoolClass?->name); ?> · <?php echo e($assessment->subject?->name); ?> · <?php echo e($assessment->term?->name); ?></p>

    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('grading.mark-entry-grid', ['assessment' => $assessment]);

$__html = app('livewire')->mount($__name, $__params, 'lw-1534175718-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Allan\smart_accountant2\app\Modules\Grading\Providers/../Resources/views/mark-entry/show.blade.php ENDPATH**/ ?>