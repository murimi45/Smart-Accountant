 @include('layouts.header')
<body class="dashboard">
    <div class="full_container">
        <div class="inner_container">
            <!-- Sidebar -->
            <nav id="sidebar">
                <div class="sidebar_blog_1">
                   
                    <div class="sidebar_user_info">
                        <div class="user_profle_side">
                            <div class="user_img">
                                <img class="img-responsive" src="{{ asset('images/layout_img/user_img.jpg') }}" alt="User" />
                            </div>
                            <div class="user_info">
                                <h6>{{ auth()->user()->school->school_name }}</h6>
                                <p><span class="online_animation"></span> Online</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="sidebar_blog_2">
                    <h4>General</h4>
                    <ul class="list-unstyled components">
                        <!-- Dashboard -->
                        <li>
                            <a href="{{ url('/dashboard') }}" 
                               class="nav-link {{ Request::segment(1) == 'dashboard' ? 'active' : '' }}">
                                <i class="fa fa-tachometer blue_color"></i> 
                                <span>Dashboard</span>
                            </a>
                        </li>

                        <!-- Levels -->
                        <li>
                            <a href="#levelMenu" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                                <i class="fa fa-graduation-cap purple_color"></i> 
                                <span>Levels</span>
                            </a>
                            <ul class="collapse list-unstyled" id="levelMenu">
                                <li><a href="{{ url('/class') }}"><span>Class Levels</span></a></li>
                                <li><a href="{{ url('/term') }}"><span>Term Levels</span></a></li>
                            </ul>
                        </li>

                        <!-- Students -->
                        <li>
                            <a href="{{ url('/student') }}" 
                               class="nav-link {{ Request::segment(1) == 'student' ? 'active' : '' }}">
                                <i class="fa fa-users orange_color"></i> 
                                <span>Students</span>
                            </a>
                        </li>

                        <!-- Fee Module -->
                        <li>
                            <a href="#feeMenu" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                                <i class="fa fa-credit-card-alt red_color"></i> 
                                <span>Fees</span>
                            </a>
                            <ul class="collapse list-unstyled" id="feeMenu">
                                <li><a href="{{ url('/classfee') }}"><span>Class Fees</span></a></li>
                                <li><a href="{{ url('/extrafee') }}"><span>Extra Fees</span></a></li>
                                <li><a href="{{ url('/listextrafeestudents') }}"><span>Assign Extra Fees</span></a></li>
                                <li><a href="{{ url('/invoices') }}"><span>Fee Summary</span></a></li>
                            </ul>
                        </li>

                        <!-- Expenses -->
                        <li>
                            <a href="#expensesMenu" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                                <i class="fa fa-shopping-cart purple_color"></i> 
                                <span>Expenses</span>
                            </a>
                            <ul class="collapse list-unstyled" id="expensesMenu">
                                <li><a href="{{ route('expense_categories.index') }}"><span>Expense Categories</span></a></li>
                                <li><a href="{{ route('expenses.index') }}"><span>All Expenses</span></a></li>
                            </ul>
                        </li>

                        <!-- Income -->
                        <li>
                            <a href="#incomeMenu" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                                <i class="fa fa-line-chart green_color"></i> 
                                <span>Income</span>
                            </a>
                            <ul class="collapse list-unstyled" id="incomeMenu">
                                <li><a href="{{ route('income_categories.index') }}"><span>Income Categories</span></a></li>
                                <li><a href="{{ route('other_incomes.index') }}"><span>Other Income</span></a></li>
                            </ul>
                        </li>

                        <!-- Cashbook -->
                        <li>
                            <a href="#cashbookMenu" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                                <i class="fa fa-book blue_color"></i> 
                                <span>Cashbook</span>
                            </a>
                            <ul class="collapse list-unstyled" id="cashbookMenu">
                                <li><a href="{{ route('cashbook.index') }}"><span>Cashbook Entries</span></a></li>
                            </ul>
                        </li>

                        <!-- Settings -->
                        <li>
                            <a href="#settingsMenu" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                                <i class="fa fa-cog orange_color"></i> 
                                <span>Settings</span>
                            </a>
                            <ul class="collapse list-unstyled" id="settingsMenu">
                                <li><a href="{{ route('sms.logs') }}"><span>SmsLogs</span></a></li>
                            </ul>
                            <ul class="collapse list-unstyled" id="settingsMenu">
                                <li><a href="{{ route('payment_channels.index') }}"><span>Payment Channels</span></a></li>
                            </ul>
                        </li>

                        <li>
                            <a href="{{ route('sms.logs') }}" 
                               class="nav-link {{ Request::is('sms/logs') ? 'active' : '' }}">
                                <i class="fa fa-users orange_color"></i> 
                                <span>SmsLogs</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
            <!-- End Sidebar -->

            <!-- Right Content -->
            <div id="content">
                

                 
                <!-- Middle Content -->
                <div class="midde_cont">
                        @yield('main')  
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom Dashboard JS -->
    <script src="/js/dashboard.js"></script>
    
    @livewireScripts

</body>
</html>