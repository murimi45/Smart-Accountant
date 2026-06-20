{{-- Summary Cards --}}
<div class="row mb-4">
    <div class="col-md-4 mb-3 mb-md-0">
        <div class="summary-card">
            <div class="summary-icon icon-income">
                <i class="fa fa-arrow-up"></i>
            </div>
            <div class="summary-content">
                <div class="summary-value">KSh {{ number_format($report['totalIncome'], 2) }}</div>
                <div class="summary-label">Total Income</div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3 mb-md-0">
        <div class="summary-card">
            <div class="summary-icon icon-expense">
                <i class="fa fa-arrow-down"></i>
            </div>
            <div class="summary-content">
                <div class="summary-value">KSh {{ number_format($report['totalExpenses'], 2) }}</div>
                <div class="summary-label">Total Expenses</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="summary-card">
            <div class="summary-icon {{ $report['netSurplus'] >= 0 ? 'icon-surplus' : 'icon-deficit' }}">
                <i class="fa fa-{{ $report['netSurplus'] >= 0 ? 'check' : 'exclamation' }}"></i>
            </div>
            <div class="summary-content">
                <div class="summary-value {{ $report['netSurplus'] >= 0 ? 'value-positive' : 'value-negative' }}">
                    KSh {{ number_format($report['netSurplus'], 2) }}
                </div>
                <div class="summary-label">Net {{ $report['netSurplus'] >= 0 ? 'Surplus' : 'Deficit' }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Income / Expense Breakdown --}}
<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card form-card h-100">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fa fa-arrow-trend-up me-2"></i>Income
                </h5>
            </div>
            <div class="card-body p-0">
                <table class="table report-table mb-0">
                    <tbody>
                        @forelse($report['incomeRows'] as $row)
                            <tr>
                                <td>{{ $row['account']->name }}</td>
                                <td class="text-end">{{ number_format($row['amount'], 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="text-center py-3 text-muted">No income in period</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="fw-bold total-row">
                            <td>Total Income</td>
                            <td class="text-end">{{ number_format($report['totalIncome'], 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="card form-card h-100">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fa fa-arrow-trend-down me-2"></i>Expenses
                </h5>
            </div>
            <div class="card-body p-0">
                <table class="table report-table mb-0">
                    <tbody>
                        @forelse($report['expenseRows'] as $row)
                            <tr>
                                <td>{{ $row['account']->name }}</td>
                                <td class="text-end">{{ number_format($row['amount'], 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="text-center py-3 text-muted">No expenses in period</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="fw-bold total-row">
                            <td>Total Expenses</td>
                            <td class="text-end">{{ number_format($report['totalExpenses'], 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
/* Base Variables — shared with expense form, balance-sheet & financial-report wrapper */
:root {
    --primary-color: #36a9e2;
    --success-color: #79c347;
    --success-dark: #5fa732;
    --danger-color: #ef4444;
    --warning-color: #f59e0b;
    --gray-50: #f9fafb;
    --gray-100: #f3f4f6;
    --gray-200: #e5e7eb;
    --gray-300: #d1d5db;
    --gray-500: #6b7280;
    --gray-600: #4b5563;
    --gray-700: #374151;
    --gray-900: #111827;
    --border-radius: 8px;
}

/* Summary Cards */
.summary-card {
    display: flex;
    align-items: center;
    gap: 16px;
    height: 100%;
    background: #fff;
    border: 1px solid var(--gray-200);
    border-radius: var(--border-radius);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    padding: 20px;
}

.summary-icon {
    flex-shrink: 0;
    width: 44px;
    height: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: var(--border-radius);
    font-size: 16px;
}

.icon-income {
    background: rgba(54, 169, 226, 0.1);
    color: var(--primary-color);
}

.icon-expense {
    background: rgba(239, 68, 68, 0.1);
    color: var(--danger-color);
}

.icon-surplus {
    background: rgba(121, 195, 71, 0.12);
    color: var(--success-dark);
}

.icon-deficit {
    background: rgba(239, 68, 68, 0.1);
    color: var(--danger-color);
}

.summary-value {
    font-size: 20px;
    font-weight: 700;
    color: var(--gray-900);
    line-height: 1.2;
}

.value-positive {
    color: var(--success-dark);
}

.value-negative {
    color: var(--danger-color);
}

.summary-label {
    font-size: 13px;
    color: var(--gray-500);
    margin-top: 2px;
}

/* Income / Expense Cards */
.form-card {
    border: 1px solid var(--gray-200);
    border-radius: var(--border-radius);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.form-card .card-header {
    background: var(--gray-50);
    border-bottom: 1px solid var(--gray-200);
    padding: 16px 24px;
}

.form-card .card-header h5 {
    font-size: 16px;
    font-weight: 600;
    color: var(--gray-900);
    margin: 0;
}

/* Report Table */
.report-table {
    font-size: 14px;
    color: var(--gray-700);
}

.report-table td {
    padding: 10px 24px;
    border-color: var(--gray-100);
}

.report-table tfoot .total-row td {
    background: var(--gray-50);
    border-top: 2px solid var(--gray-300);
    border-bottom: none;
    color: var(--gray-900);
}

/* Responsive Design */
@media (max-width: 768px) {
    .summary-card {
        padding: 16px;
    }

    .summary-value {
        font-size: 18px;
    }

    .report-table td {
        padding: 10px 16px;
    }
}
</style>