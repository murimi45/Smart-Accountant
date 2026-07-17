<?php if (\Illuminate\Support\Facades\Blade::check('role', 'admin')): ?>
<div class="sidebar_blog_2">
    <h4>School Setup</h4>
    <ul class="list-unstyled components">
        <li>
            <a href="#usersMenu" data-toggle="collapse" aria-expanded="<?php echo e(Request::is('admins*') || Request::is('accountants*') ? 'true' : 'false'); ?>" class="dropdown-toggle">
                <i class="fa fa-users purple_color"></i>
                <span>Users</span>
            </a>
            <ul class="collapse list-unstyled <?php echo e(Request::is('admins*') || Request::is('accountants*') ? 'show' : ''); ?>" id="usersMenu">
                <li><a class="<?php echo e(Request::is('admins*') ? 'active' : ''); ?>" href="<?php echo e(route('admins.index')); ?>">Admins</a></li>
                <li><a class="<?php echo e(Request::is('accountants*') ? 'active' : ''); ?>" href="<?php echo e(route('accountants.index')); ?>">Accountants</a></li>
            </ul>
        </li>
        <li>
            <a href="#levelMenu" data-toggle="collapse" aria-expanded="<?php echo e(Request::is('class*') || Request::is('term*') || Request::is('streams*') || Request::is('academic-years*') ? 'true' : 'false'); ?>" class="dropdown-toggle">
                <i class="fa fa-graduation-cap purple_color"></i>
                <span>Academic Setup</span>
            </a>
            <ul class="collapse list-unstyled <?php echo e(Request::is('class*') || Request::is('term*') || Request::is('streams*') || Request::is('academic-years*') ? 'show' : ''); ?>" id="levelMenu">
                <li><a class="<?php echo e(Request::is('class*') ? 'active' : ''); ?>" href="<?php echo e(url('/class')); ?>">Class Levels</a></li>
                <li><a class="<?php echo e(Request::is('term*') ? 'active' : ''); ?>" href="<?php echo e(url('/term')); ?>">Term Levels</a></li>
                <li><a class="<?php echo e(Request::is('streams*') ? 'active' : ''); ?>" href="<?php echo e(route('streams.index')); ?>">Streams</a></li>
                <li><a class="<?php echo e(Request::is('academic-years*') ? 'active' : ''); ?>" href="<?php echo e(url('/academic-years')); ?>">Academic Years</a></li>
            </ul>
        </li>
        <?php if (\Illuminate\Support\Facades\Blade::check('module', 'accountant')): ?>
        <li>
            <a href="<?php echo e(route('payment_channels.index')); ?>" class="<?php echo e(Request::is('payment_channels*') ? 'active' : ''); ?>">
                <i class="fa fa-cog orange_color"></i>
                <span>Payment Channels</span>
            </a>
        </li>
        <?php endif; ?>
    </ul>
</div>
<?php endif; ?>
<?php /**PATH C:\Users\Allan\smart_accountant2\resources\views/layouts/partials/sidebar/school-setup.blade.php ENDPATH**/ ?>