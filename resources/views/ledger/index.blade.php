@extends('layouts.app')

@section('main')
<div class="main-wrapper">
    {{-- Page Header --}}
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h4 class="mb-1">General Ledger</h4>
                <p class="text-muted mb-0">Double-entry lines posted automatically from cashbook transactions</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="{{ route('accounts.index') }}" class="btn btn-outline-secondary">
                    <i class="fa fa-list me-2"></i>Chart of Accounts
                </a>
            </div>
        </div>
    </div>

    {{-- Filters Card --}}
    <div class="card filter-card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('ledger.index') }}" class="filter-form">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label"><i class="fa fa-book me-1"></i>Account</label>
                        <select name="account_id" class="form-select">
                            <option value="">All accounts</option>
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}" {{ (string) $accountId === (string) $account->id ? 'selected' : '' }}>
                                    {{ $account->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label"><i class="fa fa-calendar me-1"></i>From</label>
                        <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label"><i class="fa fa-calendar me-1"></i>To</label>
                        <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
                    </div>
                    <div class="col-md-2">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="fa fa-filter me-1"></i>Filter
                            </button>
                            <a href="{{ route('ledger.index') }}" class="btn btn-outline-secondary" title="Reset">
                                <i class="fa fa-redo"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="summary-card-secondary">
                <div class="summary-content-secondary">
                    <div class="summary-value-secondary">KSh {{ number_format($totalDebit, 2) }}</div>
                    <div class="summary-label-secondary">Total Debits</div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="summary-card-secondary">
                <div class="summary-content-secondary">
                    <div class="summary-value-secondary">KSh {{ number_format($totalCredit, 2) }}</div>
                    <div class="summary-label-secondary">Total Credits</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Ledger Entries Table --}}
    <div class="card table-card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fa fa-book me-2"></i>Ledger Entries
                </h5>
                <span class="badge bg-light text-dark">{{ $entries->total() }} Lines</span>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table admin-table mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Account</th>
                            <th>Description</th>
                            <th class="text-end">Debit</th>
                            <th class="text-end">Credit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($entries as $entry)
                            <tr>
                                <td>
                                    <div class="admin-name">{{ $entry->transaction_date?->format('d M Y') }}</div>
                                </td>
                                <td>
                                    <span class="role-badge">{{ $entry->account?->name }}</span>
                                </td>
                                <td>
                                    <span class="email-text">{{ $entry->description }}</span>
                                </td>
                                <td class="text-end">
                                    @if((float) $entry->debit > 0)
                                        <span class="amount-debit">{{ number_format($entry->debit, 2) }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if((float) $entry->credit > 0)
                                        <span class="amount-credit">{{ number_format($entry->credit, 2) }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="fa fa-inbox fa-3x mb-3"></i>
                                        <p class="mb-2">No ledger entries yet</p>
                                        <small class="d-block mb-3">Postings are created when cashbook transactions are recorded</small>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        @if($entries->hasPages())
        <div class="card-footer">
            {{ $entries->links() }}
        </div>
        @endif
    </div>
</div>

<style>
/* Base Variables — matched to Admin Management page */
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
    --gray-400: #9ca3af;
    --gray-500: #6b7280;
    --gray-600: #4b5563;
    --gray-700: #374151;
    --gray-900: #111827;
    --border-radius: 8px;
}

/* Page Header */
.page-header h4 {
    font-size: 24px;
    font-weight: 600;
    color: var(--gray-900);
    margin: 0;
}

.page-header p {
    font-size: 14px;
    color: var(--gray-500);
}

/* Cards */
.filter-card,
.table-card {
    border: 1px solid var(--gray-200);
    border-radius: var(--border-radius);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.filter-card .card-body {
    padding: 20px;
}

.table-card .card-header {
    background: var(--gray-50);
    border-bottom: 1px solid var(--gray-200);
    padding: 16px 20px;
}

.table-card .card-header h5 {
    font-size: 16px;
    font-weight: 600;
    color: var(--gray-900);
}

.card-footer {
    background: var(--gray-50);
    border-top: 1px solid var(--gray-200);
    padding: 16px 20px;
}

/* Summary Cards */
.summary-card-secondary {
    background: white;
    border: 1px solid var(--gray-200);
    border-radius: var(--border-radius);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    padding: 20px;
    height: 100%;
    border-left: 4px solid var(--primary-color);
}

.summary-content-secondary {
    display: flex;
    flex-direction: column;
}

.summary-value-secondary {
    font-size: 22px;
    font-weight: 700;
    color: var(--gray-900);
    line-height: 1.3;
}

.summary-label-secondary {
    font-size: 13px;
    font-weight: 500;
    color: var(--gray-500);
    margin-top: 4px;
}

/* Form Elements */
.form-label {
    font-size: 13px;
    font-weight: 500;
    color: var(--gray-700);
    margin-bottom: 6px;
}

.form-control,
.form-select {
    border: 1px solid var(--gray-300);
    border-radius: var(--border-radius);
    padding: 8px 12px;
    font-size: 14px;
    transition: border-color 0.2s;
}

.form-control:focus,
.form-select:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(54, 169, 226, 0.1);
}

/* Buttons */
.btn {
    border-radius: var(--border-radius);
    padding: 8px 16px;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.2s;
}

.btn-primary {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
}

.btn-primary:hover {
    background-color: #2a8cbd;
    border-color: #2a8cbd;
}

.btn-success {
    background-color: var(--success-color);
    border-color: var(--success-color);
}

.btn-success:hover {
    background-color: var(--success-dark);
    border-color: var(--success-dark);
}

.btn-outline-secondary {
    color: var(--gray-600);
    border-color: var(--gray-300);
    background: white;
    padding: 8px 16px;
}

.btn-outline-secondary:hover {
    background-color: var(--gray-50);
    border-color: var(--gray-400);
    color: var(--gray-700);
}

.btn-sm {
    padding: 6px 12px;
    font-size: 13px;
}

/* Table */
.admin-table {
    font-size: 14px;
}

.admin-table thead {
    background-color: var(--gray-50);
    border-bottom: 2px solid var(--gray-200);
}

.admin-table thead th {
    font-weight: 600;
    color: var(--gray-700);
    padding: 12px 16px;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.admin-table tbody td {
    padding: 16px;
    vertical-align: middle;
    border-bottom: 1px solid var(--gray-100);
}

.admin-table tbody tr:hover {
    background-color: var(--gray-50);
}

.admin-name {
    font-weight: 500;
    color: var(--gray-900);
}

.email-text {
    color: var(--gray-600);
    font-size: 13px;
}

/* Role-style Badge (reused for Account name) */
.role-badge {
    background-color: var(--gray-100);
    color: var(--gray-700);
    padding: 4px 10px;
    border-radius: 6px;
    font-weight: 500;
    font-size: 12px;
    display: inline-block;
}

/* Debit / Credit Amounts */
.amount-debit {
    color: #991b1b;
    font-weight: 600;
}

.amount-credit {
    color: var(--success-dark);
    font-weight: 600;
}

/* Empty State */
.empty-state {
    color: var(--gray-400);
}

.empty-state i {
    opacity: 0.3;
}

.empty-state p {
    font-size: 16px;
    font-weight: 500;
    color: var(--gray-600);
}

.empty-state small {
    color: var(--gray-500);
}

/* Badge Override */
.badge.bg-light {
    background-color: var(--gray-100) !important;
    color: var(--gray-700);
    padding: 4px 12px;
    font-weight: 500;
}

/* Pagination */
.pagination {
    margin: 0;
    display: flex;
    list-style: none;
    padding: 0;
}

.pagination .page-item {
    margin: 0 2px;
}

.pagination .page-link {
    position: relative;
    display: block;
    padding: 6px 12px;
    font-size: 14px;
    font-weight: 500;
    color: var(--gray-600);
    text-decoration: none;
    background-color: white;
    border: 1px solid var(--gray-300);
    border-radius: 6px;
    transition: all 0.2s;
}

.pagination .page-link:hover {
    background-color: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
}

.pagination .page-item.active .page-link {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
    color: white;
}

.pagination .page-item.disabled .page-link {
    color: var(--gray-400);
    background-color: var(--gray-50);
    border-color: var(--gray-200);
    cursor: not-allowed;
    pointer-events: none;
}

.pagination .page-link svg {
    display: none;
}

.pagination .page-item:first-child .page-link::before {
    content: '← Previous';
    font-size: 13px;
}

.pagination .page-item:last-child .page-link::before {
    content: 'Next →';
    font-size: 13px;
}

.pagination .page-item:first-child .page-link,
.pagination .page-item:last-child .page-link {
    font-size: 0;
}

.pagination .page-item:first-child .page-link::before,
.pagination .page-item:last-child .page-link::before {
    font-size: 13px;
}

/* Responsive Design */
@media (max-width: 768px) {
    .admin-table {
        font-size: 13px;
    }

    .admin-table thead th,
    .admin-table tbody td {
        padding: 10px;
    }

    .summary-value-secondary {
        font-size: 18px;
    }

    .pagination .page-item:first-child .page-link::before {
        content: '←';
    }

    .pagination .page-item:last-child .page-link::before {
        content: '→';
    }
}
</style>
@endsection