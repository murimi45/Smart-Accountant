<div class="card form-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="fa fa-scale-balanced me-2"></i>Trial Balance
        </h5>
        @if($report['balanced'])
            <span class="badge badge-balanced">
                <i class="fa fa-check-circle me-1"></i>Balanced
            </span>
        @else
            <span class="badge badge-check">
                <i class="fa fa-exclamation-triangle me-1"></i>Out of balance
            </span>
        @endif
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table report-table mb-0">
                <thead>
                    <tr>
                        <th>Account</th>
                        <th>Category</th>
                        <th class="text-end">Debit (KSh)</th>
                        <th class="text-end">Credit (KSh)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($report['rows'] as $row)
                        <tr>
                            <td>{{ $row['account']->name }}</td>
                            <td>{{ ucfirst($row['category']) }}</td>
                            <td class="text-end">{{ $row['debit'] > 0 ? number_format($row['debit'], 2) : '-' }}</td>
                            <td class="text-end">{{ $row['credit'] > 0 ? number_format($row['credit'], 2) : '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">No ledger activity for this period.</td>
                        </tr>
                    @endforelse
                </tbody>
                @if(count($report['rows']) > 0)
                    <tfoot>
                        <tr class="fw-bold total-row">
                            <td colspan="2">Totals</td>
                            <td class="text-end">{{ number_format($report['totalDebits'], 2) }}</td>
                            <td class="text-end">{{ number_format($report['totalCredits'], 2) }}</td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>

<style>
/* Base Variables — shared with expense form, balance-sheet, profit-loss & financial-report wrapper */
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

/* Report Card */
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
    font-size: 18px;
    font-weight: 600;
    color: var(--gray-900);
    margin: 0;
}

/* Status Badges */
.badge-balanced,
.badge-check {
    font-size: 13px;
    font-weight: 500;
    padding: 6px 12px;
    border-radius: var(--border-radius);
}

.badge-balanced {
    background-color: #e8f5e1;
    color: var(--success-dark);
}

.badge-check {
    background-color: #fef3c7;
    color: #92400e;
}

/* Report Table */
.report-table {
    font-size: 14px;
    color: var(--gray-700);
}

.report-table thead th {
    background: var(--gray-50);
    border-bottom: 1px solid var(--gray-200);
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    color: var(--gray-600);
    padding: 12px 24px;
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
    .report-table thead th,
    .report-table td {
        padding: 10px 16px;
    }
}
</style>