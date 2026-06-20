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
                <h4>Overview</h4>
                <ul class="list-unstyled components">
                    <li>
                        <a href="{{ url('/dashboard') }}"
                           class="{{ Request::segment(1) == 'dashboard' ? 'active' : '' }}">
                            <i class="fa fa-tachometer blue_color"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                </ul>
            </div>

            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'accountant')
            <div class="sidebar_blog_2">
                <h4>Students</h4>
                <ul class="list-unstyled components">
                    @if(auth()->user()->role === 'admin')
                    <li>
                        <a href="{{ url('/student') }}" class="{{ Request::is('student*') && !Request::is('bulk*') ? 'active' : '' }}">
                            <i class="fa fa-users orange_color"></i>
                            <span>Students</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('/enrollment') }}" class="{{ Request::is('enrollment*') ? 'active' : '' }}">
                            <i class="fa fa-user-plus orange_color"></i>
                            <span>Student Enrollment</span>
                        </a>
                    </li>
                    @endif
                    <li>
                        <a href="{{ route('bulk.index') }}" class="{{ Request::is('bulk*') ? 'active' : '' }}">
                            <i class="fa fa-file-csv green_color"></i>
                            <span>Bulk Import/Export</span>
                        </a>
                    </li>
                </ul>
            </div>
            @endif

            @if(auth()->user()->role === 'admin')
            <div class="sidebar_blog_2">
                <h4>School Setup</h4>
                <ul class="list-unstyled components">
                    <li>
                        <a href="#usersMenu" data-toggle="collapse" aria-expanded="{{ Request::is('admins*') || Request::is('accountants*') ? 'true' : 'false' }}" class="dropdown-toggle">
                            <i class="fa fa-users purple_color"></i>
                            <span>Users</span>
                        </a>
                        <ul class="collapse list-unstyled {{ Request::is('admins*') || Request::is('accountants*') ? 'show' : '' }}" id="usersMenu">
                            <li><a class="{{ Request::is('admins*') ? 'active' : '' }}" href="{{ route('admins.index') }}">Admins</a></li>
                            <li><a class="{{ Request::is('accountants*') ? 'active' : '' }}" href="{{ route('accountants.index') }}">Accountants</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="#levelMenu" data-toggle="collapse" aria-expanded="{{ Request::is('class*') || Request::is('term*') || Request::is('streams*') || Request::is('academic-years*') ? 'true' : 'false' }}" class="dropdown-toggle">
                            <i class="fa fa-graduation-cap purple_color"></i>
                            <span>Academic Setup</span>
                        </a>
                        <ul class="collapse list-unstyled {{ Request::is('class*') || Request::is('term*') || Request::is('streams*') || Request::is('academic-years*') ? 'show' : '' }}" id="levelMenu">
                            <li><a class="{{ Request::is('class*') ? 'active' : '' }}" href="{{ url('/class') }}">Class Levels</a></li>
                            <li><a class="{{ Request::is('term*') ? 'active' : '' }}" href="{{ url('/term') }}">Term Levels</a></li>
                            <li><a class="{{ Request::is('streams*') ? 'active' : '' }}" href="{{ route('streams.index') }}">Streams</a></li>
                            <li><a class="{{ Request::is('academic-years*') ? 'active' : '' }}" href="{{ url('/academic-years') }}">Academic Years</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="{{ route('payment_channels.index') }}" class="{{ Request::is('payment_channels*') ? 'active' : '' }}">
                            <i class="fa fa-cog orange_color"></i>
                            <span>Payment Channels</span>
                        </a>
                    </li>
                </ul>
            </div>
            @endif

            <div class="sidebar_blog_2">
                <h4>Fees</h4>
                <ul class="list-unstyled components">
                    <li>
                        <a href="#feeMenu" data-toggle="collapse" aria-expanded="{{ Request::is('classfee*') || Request::is('extrafee*') || Request::is('listextrafeestudents*') || Request::is('invoices*') || Request::is('waivers*') ? 'true' : 'false' }}" class="dropdown-toggle">
                            <i class="fa fa-credit-card-alt red_color"></i>
                            <span>Fees</span>
                        </a>
                        <ul class="collapse list-unstyled {{ Request::is('classfee*') || Request::is('extrafee*') || Request::is('listextrafeestudents*') || Request::is('invoices*') || Request::is('waivers*') ? 'show' : '' }}" id="feeMenu">
                            <li><a class="{{ Request::is('invoices*') ? 'active' : '' }}" href="{{ url('/invoices') }}">Fee Summary</a></li>
                            <li><a class="{{ Request::is('classfee*') ? 'active' : '' }}" href="{{ url('/classfee') }}">Class Fees</a></li>
                            <li><a class="{{ Request::is('extrafee*') ? 'active' : '' }}" href="{{ url('/extrafee') }}">Extra Fees</a></li>
                            <li><a class="{{ Request::is('listextrafeestudents*') ? 'active' : '' }}" href="{{ url('/listextrafeestudents') }}">Assign Extra Fees</a></li>
                            <li><a class="{{ Request::is('waivers*') ? 'active' : '' }}" href="{{ route('waivers.index') }}">Fee Waivers</a></li>
                        </ul>
                    </li>
                </ul>
            </div>

            <div class="sidebar_blog_2">
                <h4>Finance</h4>
                <ul class="list-unstyled components">
                    <li>
                        <a href="#cashbookMenu" data-toggle="collapse" aria-expanded="{{ Request::is('cashbook*') || Request::is('reconciliation*') || Request::is('accounts*') || Request::is('ledger*') ? 'true' : 'false' }}" class="dropdown-toggle">
                            <i class="fa fa-book blue_color"></i>
                            <span>Cashbook</span>
                        </a>
                        <ul class="collapse list-unstyled {{ Request::is('cashbook*') || Request::is('reconciliation*') || Request::is('accounts*') || Request::is('ledger*') ? 'show' : '' }}" id="cashbookMenu">
                            <li><a class="{{ Request::is('cashbook*') && !Request::is('reconciliation*') ? 'active' : '' }}" href="{{ route('cashbook.index') }}">Transaction History</a></li>
                            <li><a class="{{ Request::is('accounts*') ? 'active' : '' }}" href="{{ route('accounts.index') }}">Chart of Accounts</a></li>
                            <li><a class="{{ Request::is('ledger*') ? 'active' : '' }}" href="{{ route('ledger.index') }}">General Ledger</a></li>
                            <li><a class="{{ Request::is('reconciliation*') ? 'active' : '' }}" href="{{ route('reconciliation.index') }}">Bank Reconciliation</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="#incomeMenu" data-toggle="collapse" aria-expanded="{{ Request::is('income_categories*') || Request::is('other_incomes*') ? 'true' : 'false' }}" class="dropdown-toggle">
                            <i class="fa fa-line-chart green_color"></i>
                            <span>Income</span>
                        </a>
                        <ul class="collapse list-unstyled {{ Request::is('income_categories*') || Request::is('other_incomes*') ? 'show' : '' }}" id="incomeMenu">
                            <li><a class="{{ Request::is('income_categories*') ? 'active' : '' }}" href="{{ route('income_categories.index') }}">Income Categories</a></li>
                            <li><a class="{{ Request::is('other_incomes*') ? 'active' : '' }}" href="{{ route('other_incomes.index') }}">Other Income</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="#expensesMenu" data-toggle="collapse" aria-expanded="{{ Request::is('expense_categories*') || Request::is('expenses*') || Request::is('budgets*') ? 'true' : 'false' }}" class="dropdown-toggle">
                            <i class="fa fa-shopping-cart purple_color"></i>
                            <span>Expenses</span>
                        </a>
                        <ul class="collapse list-unstyled {{ Request::is('expense_categories*') || Request::is('expenses*') || Request::is('budgets*') ? 'show' : '' }}" id="expensesMenu">
                            <li><a class="{{ Request::is('expense_categories*') ? 'active' : '' }}" href="{{ route('expense_categories.index') }}">Expense Categories</a></li>
                            <li><a class="{{ Request::is('expenses*') && !Request::is('budgets*') ? 'active' : '' }}" href="{{ route('expenses.index') }}">All Expenses</a></li>
                            <li><a class="{{ Request::is('budgets*') ? 'active' : '' }}" href="{{ route('budgets.index') }}">Budgets</a></li>
                        </ul>
                    </li>
                </ul>
            </div>

            <div class="sidebar_blog_2">
                <h4>Reports</h4>
                <ul class="list-unstyled components">
                    <li>
                        <a href="#reportsMenu" data-toggle="collapse" aria-expanded="{{ Request::is('reports/*') || Request::is('audit-log*') ? 'true' : 'false' }}" class="dropdown-toggle">
                            <i class="fa fa-bar-chart blue_color"></i>
                            <span>Reports</span>
                        </a>
                        <ul class="collapse list-unstyled {{ Request::is('reports/*') || Request::is('audit-log*') ? 'show' : '' }}" id="reportsMenu">
                            <li><a class="{{ Request::is('reports/financial*') ? 'active' : '' }}" href="{{ route('reports.financial') }}">Financial Reports</a></li>
                            <li><a class="{{ Request::is('reports/budget-variance*') ? 'active' : '' }}" href="{{ route('reports.budget-variance') }}">Budget Variance</a></li>
                            <li><a class="{{ Request::is('reports/aged-debtors*') ? 'active' : '' }}" href="{{ route('reports.aged-debtors') }}">Aged Debtors</a></li>
                            <li><a class="{{ Request::is('audit-log*') ? 'active' : '' }}" href="{{ route('audit.index') }}">Audit Log</a></li>
                        </ul>
                    </li>
                </ul>
            </div>

            <div class="sidebar_blog_2">
                <h4>Communications</h4>
                <ul class="list-unstyled components">
                    <li>
                        <a href="#smsMenu" data-toggle="collapse" aria-expanded="{{ Request::is('sms/*') ? 'true' : 'false' }}" class="dropdown-toggle">
                            <i class="fa fa-envelope-o orange_color"></i>
                            <span>SMS</span>
                        </a>
                        <ul class="collapse list-unstyled {{ Request::is('sms/*') ? 'show' : '' }}" id="smsMenu">
                            <li><a class="{{ Request::is('sms/logs*') ? 'active' : '' }}" href="{{ route('sms.logs') }}">SMS Logs</a></li>
                            <li><a class="{{ Request::is('sms/reminders*') ? 'active' : '' }}" href="{{ route('reminders.edit') }}">Fee Reminders</a></li>
                        </ul>
                    </li>
                </ul>
            </div>

            <div class="sidebar_blog_3">
                <ul class="list-unstyled components">
                    <li>
                        <form id="logout-form" action="{{ route('logout.and.login') }}" method="POST" style="display: inline;">
                            @csrf
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
                @yield('main')
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="/js/dashboard.js"></script>
@livewireScripts
</body>
</html>
