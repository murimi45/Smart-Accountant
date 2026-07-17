<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Platform — School modules</title>
    <link rel="stylesheet" href="<?php echo e(asset('css/bootstrap.min.css')); ?>">
</head>
<body class="bg-light">
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">School modules</h1>
            <p class="text-muted mb-0">Enable or disable product modules per school.</p>
        </div>
        <a href="/login" class="btn btn-outline-secondary btn-sm">Back to login</a>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>School</th>
                        <th>Email</th>
                        <?php $__currentLoopData = $modules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <th><?php echo e($module->name); ?></th>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $schools; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $school): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($school->school_name); ?></td>
                            <td><?php echo e($school->email); ?></td>
                            <?php $__currentLoopData = $modules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $pivot = $school->schoolModules->firstWhere('module_id', $module->id);
                                    $entitled = $pivot && $pivot->isEntitled();
                                ?>
                                <td>
                                    <?php if($entitled): ?>
                                        <span class="badge badge-success"><?php echo e($pivot->status); ?></span>
                                        <form method="POST" action="<?php echo e(route('platform.school-modules.disable', [$school, $module->slug])); ?>" class="d-inline">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Disable</button>
                                        </form>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">off</span>
                                        <form method="POST" action="<?php echo e(route('platform.school-modules.enable', [$school, $module->slug])); ?>" class="d-inline">
                                            <?php echo csrf_field(); ?>
                                            <input type="hidden" name="status" value="active">
                                            <button type="submit" class="btn btn-sm btn-outline-primary">Enable</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="<?php echo e(2 + $modules->count()); ?>">No schools found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        <?php echo e($schools->links()); ?>

    </div>
</div>
</body>
</html>
<?php /**PATH C:\Users\Allan\smart_accountant2\resources\views/platform/school-modules/index.blade.php ENDPATH**/ ?>