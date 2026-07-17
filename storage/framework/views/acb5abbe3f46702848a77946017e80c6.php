<?php
    $showStudentLinks = auth()->user()->hasRole('admin');
    $showBulk = auth()->user()->hasRole('admin', 'accountant')
        && auth()->user()->school?->hasModule(\App\Models\Module::SLUG_ACCOUNTANT);
?>
<?php if($showStudentLinks || $showBulk): ?>
<div class="sidebar_blog_2">
    <h4>Students</h4>
    <ul class="list-unstyled components">
        <?php if($showStudentLinks): ?>
        <li>
            <a href="<?php echo e(url('/student')); ?>" class="<?php echo e(Request::is('student*') && !Request::is('bulk*') ? 'active' : ''); ?>">
                <i class="fa fa-users orange_color"></i>
                <span>Students</span>
            </a>
        </li>
        <li>
            <a href="<?php echo e(url('/enrollment')); ?>" class="<?php echo e(Request::is('enrollment*') ? 'active' : ''); ?>">
                <i class="fa fa-user-plus orange_color"></i>
                <span>Student Enrollment</span>
            </a>
        </li>
        <?php endif; ?>

        <?php if($showBulk): ?>
        <li>
            <a href="<?php echo e(route('bulk.index')); ?>" class="<?php echo e(Request::is('bulk*') ? 'active' : ''); ?>">
                <i class="fa fa-file-csv green_color"></i>
                <span>Bulk Import/Export</span>
            </a>
        </li>
        <?php endif; ?>
    </ul>
</div>
<?php endif; ?>
<?php /**PATH C:\Users\Allan\smart_accountant2\resources\views/layouts/partials/sidebar/students.blade.php ENDPATH**/ ?>