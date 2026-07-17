<?php echo $__env->make('layouts.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<body class="dashboard">
<div class="full_container">
    <div class="inner_container">

        <!-- Sidebar -->
        <nav id="sidebar">
            <div class="sidebar_blog_1">
                <div class="sidebar_user_info">
                    <div class="user_profle_side">
                        <div class="user_img">
                            <img class="img-responsive" src="<?php echo e(asset('images/layout_img/user_img.jpg')); ?>" alt="User" />
                        </div>
                        <div class="user_info">
                            <h6><?php echo e(auth()->user()->school?->school_name); ?></h6>
                            <p><span class="online_animation"></span> Online</p>
                        </div>
                    </div>
                </div>
            </div>

            <?php echo $__env->make('layouts.partials.sidebar.overview', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php echo $__env->make('layouts.partials.sidebar.students', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php echo $__env->make('layouts.partials.sidebar.school-setup', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php echo $__env->make('layouts.partials.sidebar.finance', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php echo $__env->make('layouts.partials.sidebar.hr', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php echo $__env->make('layouts.partials.sidebar.grading', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

            <div class="sidebar_blog_3">
                <ul class="list-unstyled components">
                    <li>
                        <form id="logout-form" action="<?php echo e(route('logout.and.login')); ?>" method="POST" style="display: inline;">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="btn btn-link text-start" style="color: #fff; text-decoration: none; padding-left: 20px;">
                                <i class="fa fa-sign-out red_color"></i>
                                <span>Logout & Login</span>
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </nav>
        <!-- End Sidebar -->


        <!-- Right Content -->
        <div id="content">
            <div class="topbar">
                <nav class="navbar navbar-light">
                    <div class="d-flex align-items-center w-100 px-3">
                        <button type="button" id="sidebarCollapse" class="sidebar_toggle" aria-label="Toggle menu">
                            <i class="fa fa-bars"></i>
                        </button>
                        <div class="ms-3">
                            <span class="text-muted d-none d-md-inline"><?php echo e(auth()->user()->admin_name); ?></span>
                        </div>
                    </div>
                </nav>
            </div>
            <div class="midde_cont">
                <?php echo $__env->yieldContent('main'); ?>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="/js/dashboard.js"></script>
<?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>

</body>
</html>
<?php /**PATH C:\Users\Allan\smart_accountant2\resources\views/layouts/app.blade.php ENDPATH**/ ?>