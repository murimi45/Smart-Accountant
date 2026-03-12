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
                            <h6><?php echo e(auth()->user()->school->school_name); ?></h6>
                            <p><span class="online_animation"></span> Online</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="sidebar_blog_2">
                <h4>General</h4>
                <ul class="list-unstyled components">

                    <!-- Dashboard (Shared) -->
                    <li>
                        <a href="<?php echo e(url('/dashboard')); ?>"
                           class="<?php echo e(Request::segment(1) == 'dashboard' ? 'active' : ''); ?>">
                            <i class="fa fa-tachometer blue_color"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    <!-- Levels (Admin Only) -->
                    <?php if(auth()->user()->role === 'admin'): ?>


                        <li>
                               <a href="#usersMenu" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                                   <i class="fa fa-users purple_color"></i>
                                   <span>Users</span>
                               </a>
                               <ul class="collapse list-unstyled" id="usersMenu">
                                   <li><a href="<?php echo e(route('admins.index')); ?>">Admins</a></li>
                                   <li><a href="<?php echo e(route('accountants.index')); ?>">Accountants</a></li>
                               </ul>
                        </li>
                        <li>
                            <a href="#levelMenu" data-toggle="collapse" aria-expanded="<?php echo e(Request::is('class*') || Request::is('term*') ? 'true' : 'false'); ?>" class="dropdown-toggle">
                                <i class="fa fa-graduation-cap purple_color"></i>
                                <span>Levels</span>
                            </a>
                            <ul class="collapse list-unstyled <?php echo e(Request::is('class*') || Request::is('term*') ? 'show' : ''); ?>" id="levelMenu">
                                <li><a class="<?php echo e(Request::is('class*') ? 'active' : ''); ?>" href="<?php echo e(url('/class')); ?>">Class Levels</a></li>
                                <li><a class="<?php echo e(Request::is('term*') ? 'active' : ''); ?>" href="<?php echo e(url('/term')); ?>">Term Levels</a></li>
                            </ul>
                        </li>

                        <!-- Students (Admin Only) -->
                        <li>
                            <a href="<?php echo e(url('/student')); ?>" class="<?php echo e(Request::is('student*') ? 'active' : ''); ?>">
                                <i class="fa fa-users orange_color"></i>
                                <span>Students</span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <!-- Fees (Shared) -->
                    <li>
                        <a href="#feeMenu" data-toggle="collapse" aria-expanded="<?php echo e(Request::is('classfee*') || Request::is('extrafee*') || Request::is('listextrafeestudents*') || Request::is('invoices*') ? 'true' : 'false'); ?>" class="dropdown-toggle">
                            <i class="fa fa-credit-card-alt red_color"></i>
                            <span>Fees</span>
                        </a>
                        <ul class="collapse list-unstyled <?php echo e(Request::is('classfee*') || Request::is('extrafee*') || Request::is('listextrafeestudents*') || Request::is('invoices*') ? 'show' : ''); ?>" id="feeMenu">
                            <li><a class="<?php echo e(Request::is('classfee*') ? 'active' : ''); ?>" href="<?php echo e(url('/classfee')); ?>">Class Fees</a></li>
                            <li><a class="<?php echo e(Request::is('extrafee*') ? 'active' : ''); ?>" href="<?php echo e(url('/extrafee')); ?>">Extra Fees</a></li>
                            <li><a class="<?php echo e(Request::is('listextrafeestudents*') ? 'active' : ''); ?>" href="<?php echo e(url('/listextrafeestudents')); ?>">Assign Extra Fees</a></li>
                            <li><a class="<?php echo e(Request::is('invoices*') ? 'active' : ''); ?>" href="<?php echo e(url('/invoices')); ?>">Fee Summary</a></li>
                        </ul>
                    </li>

                    <!-- Expenses (Shared) -->
                    <li>
                        <a href="#expensesMenu" data-toggle="collapse" aria-expanded="<?php echo e(Request::is('expense_categories*') || Request::is('expenses*') ? 'true' : 'false'); ?>" class="dropdown-toggle">
                            <i class="fa fa-shopping-cart purple_color"></i>
                            <span>Expenses</span>
                        </a>
                        <ul class="collapse list-unstyled <?php echo e(Request::is('expense_categories*') || Request::is('expenses*') ? 'show' : ''); ?>" id="expensesMenu">
                            <li><a class="<?php echo e(Request::is('expense_categories*') ? 'active' : ''); ?>" href="<?php echo e(route('expense_categories.index')); ?>">Expense Categories</a></li>
                            <li><a class="<?php echo e(Request::is('expenses*') ? 'active' : ''); ?>" href="<?php echo e(route('expenses.index')); ?>">All Expenses</a></li>
                        </ul>
                    </li>

                    <!-- Income (Shared) -->
                    <li>
                        <a href="#incomeMenu" data-toggle="collapse" aria-expanded="<?php echo e(Request::is('income_categories*') || Request::is('other_incomes*') ? 'true' : 'false'); ?>" class="dropdown-toggle">
                            <i class="fa fa-line-chart green_color"></i>
                            <span>Income</span>
                        </a>
                        <ul class="collapse list-unstyled <?php echo e(Request::is('income_categories*') || Request::is('other_incomes*') ? 'show' : ''); ?>" id="incomeMenu">
                            <li><a class="<?php echo e(Request::is('income_categories*') ? 'active' : ''); ?>" href="<?php echo e(route('income_categories.index')); ?>">Income Categories</a></li>
                            <li><a class="<?php echo e(Request::is('other_incomes*') ? 'active' : ''); ?>" href="<?php echo e(route('other_incomes.index')); ?>">Other Income</a></li>
                        </ul>
                    </li>

                    <!-- Cashbook (Shared) -->
                    <li>
                        <a href="<?php echo e(route('cashbook.index')); ?>" class="<?php echo e(Request::is('cashbook*') ? 'active' : ''); ?>">
                            <i class="fa fa-book blue_color"></i>
                            <span>Cashbook</span>
                        </a>
                    </li>

                    <!-- Payment Channels (Admin Only) -->
                    <?php if(auth()->user()->role === 'admin'): ?>
                        <li>
                            <a href="<?php echo e(route('payment_channels.index')); ?>" class="<?php echo e(Request::is('payment_channels*') ? 'active' : ''); ?>">
                                <i class="fa fa-cog orange_color"></i>
                                <span>Payment Channels</span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <!-- SMS Logs (Standalone Shared) -->
                    <li>
                        <a href="<?php echo e(route('sms.logs')); ?>" class="<?php echo e(Request::is('sms/logs*') ? 'active' : ''); ?>">
                            <i class="fa fa-envelope-o orange_color"></i>
                            <span>SMS Logs</span>
                        </a>
                    </li>

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