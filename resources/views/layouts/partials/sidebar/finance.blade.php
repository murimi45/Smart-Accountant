@module('accountant')
    @role('admin', 'accountant')
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
    @endrole
@endmodule
