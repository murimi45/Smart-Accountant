@extends('layouts.app')
@section('main')

<div class="main-wrapper">
    {{-- Page Header --}}
    <div class="page-header mb-4">
        <h4 class="mb-1">Student Invoices</h4>
        <p class="text-muted mb-0">Manage student fees, payments, and statements</p>
    </div>

    {{-- Alert Messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="fa fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="fa fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($currentTerm && !request('term_id'))
        <div class="alert alert-info alert-dismissible fade show mb-4" role="alert">
            <i class="fa fa-info-circle me-2"></i>
            Showing <strong>current term</strong> invoices only ({{ $currentTerm->name }}@if($currentTerm->year) — {{ $currentTerm->year }}@endif).
            Prior-term balances appear here as <strong>Balance B/F</strong> after promotion.
            Select another term in the filter to view historical invoices.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Filters Card --}}
    <div class="card filter-card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('invoices.index') }}" class="filter-form">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label"><i class="fa fa-school me-1"></i>Class</label>
                        <select name="class_id" class="form-select">
                            <option value="">All Classes</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label"><i class="fa fa-calendar me-1"></i>Term</label>
                        <select name="term_id" class="form-select">
                            <option value="">Current term (default)</option>
                            @foreach($terms as $term)
                                <option value="{{ $term->id }}" {{ (request('term_id') ?: ($currentTerm->id ?? '')) == $term->id ? 'selected' : '' }}>
                                    {{ $term->name }} - {{ $term->year }}
                                    @if($currentTerm && $term->id === $currentTerm->id) (current) @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label"><i class="fa fa-search me-1"></i>Search Student</label>
                        <input type="text" name="search" class="form-control" placeholder="Enter student name" value="{{ request('search') }}">
                    </div>

                    <div class="col-md-2">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="fa fa-filter me-1"></i>Filter
                            </button>
                            <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary" title="Reset">
                                <i class="fa fa-redo"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Bulk Actions Card --}}
    <div class="card actions-card mb-4">
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <span class="text-muted me-2"><i class="fa fa-bolt me-1"></i>Bulk Actions:</span>
                
                <button type="button" 
                        class="btn btn-outline-primary btn-action" 
                        data-bs-toggle="modal" 
                        data-bs-target="#bulkPrintModal">
                    <i class="fa fa-print me-1"></i>Print All Statements
                </button>

                <form action="{{ route('balances.statements.bulk') }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="class_id" value="{{ request('class_id') }}">
                    <input type="hidden" name="term_id" value="{{ request('term_id') ?: ($currentTerm->id ?? '') }}">
                    <button type="submit" class="btn btn-outline-primary btn-action">
                        <i class="fa fa-file-pdf me-1"></i>Balance Statements
                    </button>
                </form>

                <form action="{{ route('balances.sms.send') }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="class_id" value="{{ request('class_id') }}">
                    <input type="hidden" name="term_id" value="{{ request('term_id') ?: ($currentTerm->id ?? '') }}">
                    <button type="submit" class="btn btn-outline-primary btn-action">
                        <i class="fa fa-sms me-1"></i>Send SMS
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Invoices Table --}}
    <div class="card table-card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fa fa-file-invoice-dollar me-2"></i>Student Fee Summary
                </h5>
                <span class="badge bg-light text-dark">{{ count($invoices) }} Students</span>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table invoice-table mb-0">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Class</th>
                            <th>Total Fee</th>
                            <th>Paid</th>
                            <th>Balance</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $i => $invoice)
                            @php
                                $student = $invoice->student;
                                $className = $invoice->enrollment?->schoolClass?->name;
                                if ($className && $invoice->enrollment?->stream?->name) {
                                    $className .= ' ' . $invoice->enrollment->stream->name;
                                }
                                $paid = $invoice->amount_paid;
                                $balance = $invoice->balance;
                                $canPay = $invoice->isCollectible()
                                    && $currentTerm
                                    && (int) $invoice->term_id === (int) $currentTerm->id;
                                $rowId = "details-row-{$i}";
                                $paymentRowId = "payment-row-{$i}";
                            @endphp

                            {{-- Summary Row --}}
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar">
                                            
                                            {{ strtoupper(substr($student?->full_name ?? '?', 0, 1)) }}
                                            
                                        </div>
                                        <div class="ms-3">
                                            <div class="student-name">{{ $student?->full_name ?? '—' }}</div>
                                            <div class="student-id">{{ $student?->admission ?? '—' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge-class">{{ $className ?? '—' }}</span>
                                </td>
                                <td>
                                    <span class="amount-text">KSh {{ number_format($invoice->total_amount, 2) }}</span>
                                </td>
                                <td>
                                    <span class="amount-text text-success">KSh {{ number_format($paid, 2) }}</span>
                                </td>
                                <td>
                                    @if($invoice->status === 'transferred')
                                        <span class="badge bg-secondary">Transferred</span>
                                    @elseif($balance > 0)
                                        <span class="badge badge-danger">
                                            KSh {{ number_format($balance, 2) }}
                                        </span>
                                    @else
                                        <span class="badge badge-success">
                                            <i class="fa fa-check me-1"></i>Cleared
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button type="button"
                                                class="btn btn-sm btn-light toggle-row"
                                                data-target="{{ $rowId }}"
                                                data-label="Details"
                                                title="View Details">
                                            <i class="fa fa-eye"></i>
                                            <span class="btn-text">Details</span>
                                        </button>
                                        @if($canPay)
                                        <button type="button"
                                                class="btn btn-sm btn-success toggle-row"
                                                data-target="{{ $paymentRowId }}"
                                                data-label="Payment"
                                                title="Add Payment">
                                            <i class="fa fa-plus"></i>
                                            <span class="btn-text">Payment</span>
                                        </button>
                                        @endif
                                        <a href="{{ route('statements.single', $invoice->student->id) }}" 
                                           class="btn btn-sm btn-light" 
                                           title="Print Statement">
                                            <i class="fa fa-print"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>

                            {{-- Details Row --}}
                            <tr id="{{ $rowId }}" class="details-row d-none">
                                <td colspan="6" class="details-cell">
                                    <div class="details-content">
                                        <div class="row">
                                            {{-- Fee Breakdown --}}
                                            <div class="col-md-6 mb-3">
                                                <h6 class="section-title">
                                                    <i class="fa fa-list-ul me-2"></i>Fee Breakdown
                                                </h6>
                                                <div class="table-responsive">
                                                    <table class="table table-sm inner-table">
                                                        <thead>
                                                            <tr>
                                                                <th>Item</th>
                                                                <th class="text-end">Amount</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($invoice->items as $item)
                                                                <tr>
                                                                    <td>{{ $item->description }}</td>
                                                                    <td class="text-end">KSh {{ number_format($item->amount, 2) }}</td>
                                                                </tr>
                                                            @endforeach
                                                            <tr class="table-total">
                                                                <td class="text-end">Total</td>
                                                                <td class="text-end">KSh {{ number_format($invoice->total_amount, 2) }}</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                            {{-- Payment History --}}
                                            <div class="col-md-6 mb-3">
                                                <h6 class="section-title">
                                                    <i class="fa fa-history me-2"></i>Payment History
                                                </h6>
                                                <div class="table-responsive">
                                                    <table class="table table-sm inner-table">
                                                        <thead>
                                                            <tr>
                                                                <th>Date</th>
                                                                <th>Method</th>
                                                                <th class="text-end">Amount</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @forelse($invoice->payments as $p)
                                                                <tr>
                                                                    <td>{{ $p->payment_date }}</td>
                                                                    <td>
                                                                        <span class="badge bg-light text-dark">{{ $p->method }}</span>
                                                                    </td>
                                                                    <td class="text-end">KSh {{ number_format($p->amount, 2) }}</td>
                                                                </tr>
                                                            @empty
                                                                <tr>
                                                                    <td colspan="3" class="text-muted text-center py-3">
                                                                        <i class="fa fa-info-circle me-1"></i>No payments recorded yet
                                                                    </td>
                                                                </tr>
                                                            @endforelse
                                                            <tr class="table-total">
                                                                <td colspan="2" class="text-end">Total Paid</td>
                                                                <td class="text-end text-success">KSh {{ number_format($invoice->amount_paid, 2) }}</td>
                                                            </tr>
                                                            <tr class="table-total">
                                                                <td colspan="2" class="text-end">Balance</td>
                                                                <td class="text-end {{ $balance > 0 ? 'text-danger' : 'text-success' }}">
                                                                    {{ $balance > 0 ? 'KSh '.number_format($balance, 2) : 'Cleared' }}
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            {{-- Payment Form Row (current term only) --}}
                            @if($canPay)
                            <tr class="payment-form-row d-none" id="{{ $paymentRowId }}">
                                <td colspan="6" class="payment-cell">
                                    <div class="payment-content">
                                        <h6 class="section-title">
                                            <i class="fa fa-plus-circle me-2"></i>Add Payment
                                        </h6>
                                        <form action="{{ route('payments.store', $invoice->id) }}" method="POST" class="row g-3">
                                            @csrf
                                            <div class="col-md-4">
                                                <label class="form-label">Amount (KSh)</label>
                                                <input type="number" 
                                                       name="amount" 
                                                       class="form-control" 
                                                       placeholder="Enter amount" 
                                                       step="0.01"
                                                       required>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Payment Method</label>
                                                <select name="method" class="form-select" required>
                                                    <option value="Cash">Cash</option>
                                                    <option value="Mpesa">M-Pesa</option>
                                                    <option value="Bank">Bank Transfer</option>
                                                    <option value="Cheque">Cheque</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">&nbsp;</label>
                                                <button type="submit" class="btn btn-success w-100">
                                                    <i class="fa fa-check-circle me-1"></i>Add Payment
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="fa fa-inbox fa-3x mb-3"></i>
                                        <p class="mb-0">No invoices found</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Bulk Print Modal --}}
<div class="modal fade" id="bulkPrintModal" tabindex="-1" aria-labelledby="bulkPrintModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('statements.bulk') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="bulkPrintModalLabel">
                        <i class="fa fa-print me-2"></i>Bulk Print Statements
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="class_id" class="form-label">
                            <i class="fa fa-school me-1"></i>Select Class
                        </label>
                        <select name="class_id" id="class_id" class="form-select" required>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="term_id" class="form-label">
                            <i class="fa fa-calendar me-1"></i>Select Term
                        </label>
                        <select name="term_id" id="term_id" class="form-select" required>
                            @foreach($terms as $term)
                                <option value="{{ $term->id }}">{{ $term->name }} - {{ $term->year }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fa fa-times me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-download me-1"></i>Download Zip
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Base Variables */
:root {
    --primary-color: #36a9e2;
    --success-color: #79c347;
    --success-dark: #5fa732;
    --danger-color: #ef4444;
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
.actions-card,
.table-card {
    border: 1px solid var(--gray-200);
    border-radius: var(--border-radius);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.filter-card .card-body,
.actions-card .card-body {
    padding: 20px;
}

.table-card .card-header {
    background: var(--gray-50);
    border-bottom: 1px solid var(--gray-200);
    padding: 16px 20px;
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
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
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

.btn-outline-primary {
    color: var(--primary-color);
    border-color: var(--gray-300);
    background: white;
}

.btn-outline-primary:hover {
    background-color: var(--gray-50);
    border-color: var(--primary-color);
    color: var(--primary-color);
}

.btn-outline-secondary {
    padding: 8px 12px;
}

.btn-action {
    font-size: 13px;
}

/* Table */
.invoice-table {
    font-size: 14px;
}

.invoice-table thead {
    background-color: var(--gray-50);
    border-bottom: 2px solid var(--gray-200);
}

.invoice-table thead th {
    font-weight: 600;
    color: var(--gray-700);
    padding: 12px 16px;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.invoice-table tbody td {
    padding: 16px;
    vertical-align: middle;
    border-bottom: 1px solid var(--gray-100);
}

.invoice-table tbody tr:hover {
    background-color: var(--gray-50);
}

/* Avatar */
.avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: var(--primary-color);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 16px;
}

.student-name {
    font-weight: 500;
    color: var(--gray-900);
}

.student-id {
    font-size: 12px;
    color: var(--gray-500);
    margin-top: 2px;
}

/* Badges */
.badge-class {
    background-color: var(--gray-100);
    color: var(--gray-700);
    padding: 4px 10px;
    border-radius: 6px;
    font-weight: 500;
    font-size: 12px;
    display: inline-block;
}

.badge-success {
    background-color: #e8f5e0;
    color: #3d7a1f;
    padding: 4px 10px;
    border-radius: 6px;
    font-weight: 500;
    font-size: 12px;
}

.badge-danger {
    background-color: #fee2e2;
    color: #991b1b;
    padding: 4px 10px;
    border-radius: 6px;
    font-weight: 500;
    font-size: 12px;
}

/* Amount Text */
.amount-text {
    font-weight: 500;
    color: var(--gray-700);
}

/* Action Buttons */
.action-buttons {
    display: flex;
    gap: 6px;
}

.action-buttons .btn-sm {
    padding: 6px 12px;
    font-size: 13px;
}

.btn-light {
    background-color: var(--gray-100);
    border-color: var(--gray-200);
    color: var(--gray-700);
}

.btn-light:hover {
    background-color: var(--gray-200);
    border-color: var(--gray-300);
}

/* Details Row */
.details-cell,
.payment-cell {
    background-color: var(--gray-50);
    padding: 0 !important;
}

.details-content,
.payment-content {
    padding: 24px;
}

.section-title {
    font-size: 14px;
    font-weight: 600;
    color: var(--gray-900);
    margin-bottom: 16px;
}

/* Inner Tables */
.inner-table {
    font-size: 13px;
}

.inner-table thead {
    background-color: white;
}

.inner-table thead th {
    padding: 10px;
    font-weight: 600;
    color: var(--gray-700);
    border-bottom: 2px solid var(--gray-200);
}

.inner-table tbody td {
    padding: 8px 10px;
}

.inner-table .table-total {
    background-color: white;
    font-weight: 600;
    border-top: 2px solid var(--gray-200);
}

/* Empty State */
.empty-state {
    color: var(--gray-400);
}

.empty-state i {
    opacity: 0.3;
}

/* Modal */
.modal-content {
    border: none;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
}

.modal-header {
    border-bottom: 1px solid var(--gray-200);
    padding: 20px 24px;
}

.modal-body {
    padding: 24px;
}

.modal-footer {
    border-top: 1px solid var(--gray-200);
    padding: 16px 24px;
}

/* Alerts */
.alert {
    border-radius: var(--border-radius);
    border: none;
    padding: 12px 16px;
}

.alert-success {
    background-color: #e8f5e0;
    color: #3d7a1f;
}

.alert-danger {
    background-color: #fee2e2;
    color: #991b1b;
}

/* Responsive */
@media (max-width: 768px) {
    .avatar {
        width: 32px;
        height: 32px;
        font-size: 14px;
    }
    
    .action-buttons .btn-text {
        display: none;
    }
    
    .action-buttons .btn-sm {
        padding: 6px 10px;
    }

    .invoice-table {
        font-size: 13px;
    }

    .invoice-table thead th,
    .invoice-table tbody td {
        padding: 10px;
    }
}
</style>

<script>
document.addEventListener('click', function (e) {
    const btn = e.target.closest('.toggle-row');
    if (!btn) return;

    const row = document.getElementById(btn.dataset.target);
    if (!row) return;

    row.classList.toggle('d-none');

    const textSpan = btn.querySelector('.btn-text');
    const icon = btn.querySelector('i');
    
    if (row.classList.contains('d-none')) {
        if (btn.dataset.label === "Details") {
            if (textSpan) textSpan.textContent = "Details";
            if (icon) icon.className = "fa fa-eye";
        } else {
            if (textSpan) textSpan.textContent = "Payment";
            if (icon) icon.className = "fa fa-plus";
        }
    } else {
        if (textSpan) textSpan.textContent = "Hide";
        if (icon) icon.className = "fa fa-eye-slash";
    }
});
</script>

@endsection