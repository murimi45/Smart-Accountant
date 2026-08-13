<div>
    <!--[if BLOCK]><![endif]--><?php if($message): ?><div class="alert alert-success"><?php echo e($message); ?></div><?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    <!--[if BLOCK]><![endif]--><?php if($error): ?><div class="alert alert-danger"><?php echo e($error); ?></div><?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <div class="card">
        <div class="card-body p-0">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Student</th>
                        <!--[if BLOCK]><![endif]--><?php if($competency): ?>
                            <th>Band (EE/ME/AE/BE)</th>
                            <th>Comment</th>
                        <?php else: ?>
                            <th>Score <!--[if BLOCK]><![endif]--><?php if($assessment->max_score): ?>/ <?php echo e($assessment->max_score); ?><?php endif; ?><!--[if ENDBLOCK]><![endif]--></th>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </tr>
                </thead>
                <tbody>
                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($row['student_name']); ?></td>
                            <!--[if BLOCK]><![endif]--><?php if($competency): ?>
                                <td>
                                    <select class="form-select form-select-sm" wire:model="rows.<?php echo e($i); ?>.band_code">
                                        <option value="">—</option>
                                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = ['EE','ME','AE','BE']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $band): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($band); ?>"><?php echo e($band); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                    </select>
                                </td>
                                <td><input class="form-control form-control-sm" wire:model="rows.<?php echo e($i); ?>.comment"></td>
                            <?php else: ?>
                                <td><input type="number" step="0.01" class="form-control form-control-sm" wire:model="rows.<?php echo e($i); ?>.raw_score"></td>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            <button class="btn btn-primary" wire:click="save" <?php if(!$assessment->isOpen()): echo 'disabled'; endif; ?>>Save marks</button>
            <!--[if BLOCK]><![endif]--><?php if (! ($assessment->isOpen())): ?>
                <span class="text-muted ms-2">Assessment must be open to enter marks.</span>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>
    </div>
</div>
<?php /**PATH C:\Users\Allan\smart_accountant2\app\Modules\Grading\Providers/../Resources/views/livewire/mark-entry-grid.blade.php ENDPATH**/ ?>