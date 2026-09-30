<?php if (\Illuminate\Support\Facades\Blade::check('module', 'accountant')): ?>
    <?php if (\Illuminate\Support\Facades\Blade::check('role', 'admin', 'accountant')): ?>
    <div class="sidebar_blog_2">
        <h4>Fees</h4>
        <ul class="list-unstyled components">
            <li>
                <a href="#feeMenu" data-toggle="collapse" aria-expanded="<?php echo e(Request::is('classfee*') || Request::is('extrafee*') || Request::is('listextrafeestudents*') || Request::is('invoices*') || Request::is('waivers*') ? 'true' : 'false'); ?>" class="dropdown-toggle">
                    <i class="fa fa-credit-card-alt red_color"></i>
                    <span>Fees</span>
                </a>
                <ul class="collapse list-unstyled <?php echo e(Request::is('classfee*') || Request::is('extrafee*') || Request::is('listextrafeestudents*') || Request::is('invoices*') || Request::is('waivers*') ? 'show' : ''); ?>" id="feeMenu">
                    <li><a class="<?php echo e(Request::is('invoices*') ? 'active' : ''); ?>" href="<?php echo e(url('/invoices')); ?>">Fee Summary</a></li>
                    <li><a class="<?php echo e(Request::is('classfee*') ? 'active' : ''); ?>" href="<?php echo e(url('/classfee')); ?>">Class Fees</a></li>
                    <li><a class="<?php echo e(Request::is('extrafee*') ? 'active' : ''); ?>" href="<?php echo e(url('/extrafee')); ?>">Extra Fees</a></li>
                    <li><a class="<?php echo e(Request::is('listextrafeestudents*') ? 'active' : ''); ?>" href="<?php echo e(url('/listextrafeestudents')); ?>">Assign Extra Fees</a></li>
                    <li><a class="<?php echo e(Request::is('waivers*') ? 'active' : ''); ?>" href="<?php echo e(route('waivers.index')); ?>">Fee Waivers</a></li>
                </ul>
            </li>
        </ul>
    </div>

    <div class="sidebar_blog_2">
        <h4>Finance</h4>
        <ul class="list-unstyled components">
            <li>
                <a href="#cashbookMenu" data-toggle="collapse" aria-expanded="<?php echo e(Request::is('cashbook*') || Request::is('reconciliation*') || Request::is('accounts*') || Request::is('ledger*') ? 'true' : 'false'); ?>" class="dropdown-toggle">
                    <i class="fa fa-book blue_color"></i>
                    <span>Cashbook</span>
                </a>
                <ul class="collapse list-unstyled <?php echo e(Request::is('cashbook*') || Request::is('reconciliation*') || Request::is('accounts*') || Request::is('ledger*') ? 'show' : ''); ?>" id="cashbookMenu">
                    <li><a class="<?php echo e(Request::is('cashbook*') && !Request::is('reconciliation*') ? 'active' : ''); ?>" href="<?php echo e(route('cashbook.index')); ?>">Transaction History</a></li>
                    <li><a class="<?php echo e(Request::is('accounts*') ? 'active' : ''); ?>" href="<?php echo e(route('accounts.index')); ?>">Chart of Accounts</a></li>
                    <li><a class="<?php echo e(Request::is('ledger*') ? 'active' : ''); ?>" href="<?php echo e(route('ledger.index')); ?>">General Ledger</a></li>
                    <li><a class="<?php echo e(Request::is('reconciliation*') ? 'active' : ''); ?>" href="<?php echo e(route('reconciliation.index')); ?>">Bank Reconciliation</a></li>
                </ul>
            </li>
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
            <li>
                <a href="#expensesMenu" data-toggle="collapse" aria-expanded="<?php echo e(Request::is('expense_categories*') || Request::is('expenses*') || Request::is('budgets*') ? 'true' : 'false'); ?>" class="dropdown-toggle">
                    <i class="fa fa-shopping-cart purple_color"></i>
                    <span>Expenses</span>
                </a>
                <ul class="collapse list-unstyled <?php echo e(Request::is('expense_categories*') || Request::is('expenses*') || Request::is('budgets*') ? 'show' : ''); ?>" id="expensesMenu">
                    <li><a class="<?php echo e(Request::is('expense_categories*') ? 'active' : ''); ?>" href="<?php echo e(route('expense_categories.index')); ?>">Expense Categories</a></li>
                    <li><a class="<?php echo e(Request::is('expenses*') && !Request::is('budgets*') ? 'active' : ''); ?>" href="<?php echo e(route('expenses.index')); ?>">All Expenses</a></li>
                    <li><a class="<?php echo e(Request::is('budgets*') ? 'active' : ''); ?>" href="<?php echo e(route('budgets.index')); ?>">Budgets</a></li>
                </ul>
            </li>
            <li>
    <a href="#payrollMenu" data-toggle="collapse" aria-expanded="<?php echo e(Request::is('payroll/*') ? 'true' : 'false'); ?>" class="dropdown-toggle">
        <i class="fa fa-id-badge green_color"></i>
        <span>Payroll</span>
    </a>
    <ul class="collapse list-unstyled <?php echo e(Request::is('payroll/*') ? 'show' : ''); ?>" id="payrollMenu">
        <li><a class="<?php echo e(Request::is('payroll/runs*') ? 'active' : ''); ?>" href="<?php echo e(route('payroll.runs.index')); ?>">Payroll Months</a></li>
        <li><a class="<?php echo e(Request::is('payroll/grades*') ? 'active' : ''); ?>" href="<?php echo e(route('salary_grades.index')); ?>">Salary Grades</a></li>
        <li><a class="<?php echo e(Request::is('payroll/employees*') ? 'active' : ''); ?>" href="<?php echo e(route('employees.index')); ?>">Employees</a></li>
    </ul>
</li>
        </ul>
    </div>

    <div class="sidebar_blog_2">
        <h4>Reports</h4>
        <ul class="list-unstyled components">
            <li>
                <a href="#reportsMenu" data-toggle="collapse" aria-expanded="<?php echo e(Request::is('reports/*') || Request::is('audit-log*') ? 'true' : 'false'); ?>" class="dropdown-toggle">
                    <i class="fa fa-bar-chart blue_color"></i>
                    <span>Reports</span>
                </a>
                <ul class="collapse list-unstyled <?php echo e(Request::is('reports/*') || Request::is('audit-log*') ? 'show' : ''); ?>" id="reportsMenu">
                    <li><a class="<?php echo e(Request::is('reports/financial*') ? 'active' : ''); ?>" href="<?php echo e(route('reports.financial')); ?>">Financial Reports</a></li>
                    <li><a class="<?php echo e(Request::is('reports/budget-variance*') ? 'active' : ''); ?>" href="<?php echo e(route('reports.budget-variance')); ?>">Budget Variance</a></li>
                    <li><a class="<?php echo e(Request::is('reports/aged-debtors*') ? 'active' : ''); ?>" href="<?php echo e(route('reports.aged-debtors')); ?>">Aged Debtors</a></li>
                    <li><a class="<?php echo e(Request::is('audit-log*') ? 'active' : ''); ?>" href="<?php echo e(route('audit.index')); ?>">Audit Log</a></li>
                </ul>
            </li>
        </ul>
    </div>

    <div class="sidebar_blog_2">
        <h4>Communications</h4>
        <ul class="list-unstyled components">
            <li>
                <a href="#smsMenu" data-toggle="collapse" aria-expanded="<?php echo e(Request::is('sms/*') ? 'true' : 'false'); ?>" class="dropdown-toggle">
                    <i class="fa fa-envelope-o orange_color"></i>
                    <span>SMS</span>
                </a>
                <ul class="collapse list-unstyled <?php echo e(Request::is('sms/*') ? 'show' : ''); ?>" id="smsMenu">
                    <li><a class="<?php echo e(Request::is('sms/logs*') ? 'active' : ''); ?>" href="<?php echo e(route('sms.logs')); ?>">SMS Logs</a></li>
                    <li><a class="<?php echo e(Request::is('sms/reminders*') ? 'active' : ''); ?>" href="<?php echo e(route('reminders.edit')); ?>">Fee Reminders</a></li>
                </ul>
            </li>
        </ul>
    </div>
    <?php endif; ?>
<?php endif; ?>
<?php /**PATH C:\Users\Allan\smart_accountant2\resources\views/layouts/partials/sidebar/finance.blade.php ENDPATH**/ ?>