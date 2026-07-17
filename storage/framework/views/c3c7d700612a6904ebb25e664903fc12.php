
<?php if (\Illuminate\Support\Facades\Blade::check('module', 'grading')): ?>
    <?php if (\Illuminate\Support\Facades\Blade::check('role', 'admin', 'teacher')): ?>
    <div class="sidebar_blog_2">
        <h4>Grading</h4>
        <ul class="list-unstyled components">
            <li>
                <a href="<?php echo e(route('grading.index')); ?>" class="<?php echo e(Request::is('grading*') ? 'active' : ''); ?>">
                    <i class="fa fa-pencil-square-o green_color"></i>
                    <span>Grading Home</span>
                </a>
            </li>
        </ul>
    </div>
    <?php endif; ?>
<?php endif; ?>
<?php /**PATH C:\Users\Allan\smart_accountant2\resources\views/layouts/partials/sidebar/grading.blade.php ENDPATH**/ ?>