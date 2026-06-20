<div class="row mb-4">
    <div class="col-md-4">
        <div class="summary-card">
            <div class="summary-content">
                <div class="summary-value">KSh {{ number_format($report['totalIncome'], 2) }}</div>
                <div class="summary-label">Total Income</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="summary-card">
            <div class="summary-content">
                <div class="summary-value">KSh {{ number_format($report['totalExpenses'], 2) }}</div>
                <div class="summary-label">Total Expenses</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="summary-card">
            <div class="summary-content">
                <div class="summary-value {{ $report['netSurplus'] >= 0 ? 'text-success' : 'text-danger' }}">
                    KSh {{ number_format($report['netSurplus'], 2) }}
                </div>
                <div class="summary-label">Net {{ $report['netSurplus'] >= 0 ? 'Surplus' : 'Deficit' }}</div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card table-card h-100">
            <div class="card-header"><h5 class="mb-0">Income</h5></div>
            <div class="card-body p-0">
                <table class="table mb-0">
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
                        <tr class="fw-bold">
                            <td>Total Income</td>
                            <td class="text-end">{{ number_format($report['totalIncome'], 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="card table-card h-100">
            <div class="card-header"><h5 class="mb-0">Expenses</h5></div>
            <div class="card-body p-0">
                <table class="table mb-0">
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
                        <tr class="fw-bold">
                            <td>Total Expenses</td>
                            <td class="text-end">{{ number_format($report['totalExpenses'], 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
