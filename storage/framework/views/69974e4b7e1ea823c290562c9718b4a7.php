
<?php if (\Illuminate\Support\Facades\Blade::check('module', 'hr')): ?>
    <?php if (\Illuminate\Support\Facades\Blade::check('role', 'admin', 'hr_manager')): ?>
    <div class="sidebar_blog_2">
        <h4>HR</h4>
        <ul class="list-unstyled components">
            <li>
                <a href="<?php echo e(route('hr.index')); ?>" class="<?php echo e(Request::is('hr*') ? 'active' : ''); ?>">
                    <i class="fa fa-id-badge purple_color"></i>
                    <span>HR Home</span>
                </a>
            </li>
        </ul>
    </div>
    <?php endif; ?>
<?php endif; ?>
<?php /**PATH C:\Users\Allan\smart_accountant2\resources\views/layouts/partials/sidebar/hr.blade.php ENDPATH**/ ?>