@extends('layouts.app')
@section('main')

<div class="main-wrapper">
    {{-- Page Header --}}
    <div class="page-header mb-4">
        <h4 class="mb-1">Bank Reconciliation</h4>
        <p class="text-muted mb-0">Match bank statement deposits to fee payments recorded in the cashbook</p>
    </div>

    {{-- Alerts --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Help Callout --}}
    <div class="info-callout mb-4">
        <strong><i class="fa fa-info-circle me-1"></i>Made a mistake?</strong>
        <ul class="mb-0 mt-2 small text-muted">
            <li><strong>Wrong match</strong> — click <em>Unmatch</em> on the linked payment under the deposit. The payment returns to the unmatched list; the deposit opens again.</li>
            <li><strong>Wrong deposit (typo in amount/date/ref)</strong> — select the deposit and use <em>Edit deposit</em> below.</li>
            <li><strong>Deposit entered by mistake</strong> — select it and click <em>Delete deposit</em>. This removes all links; payments are not deleted, only unmatched.</li>
        </ul>
    </div>

    {{-- Summary Cards --}}
    <div class="row mb-4">
        <div class="col-md-3 mb-3 mb-md-0">
            <div class="summary-card">
                <div class="summary-icon icon-budget">
                    <i class="fa fa-university"></i>
                </div>
                <div class="summary-content">
                    <div class="summary-value">{{ $summary['open_deposits'] }}</div>
                    <div class="summary-label">Open bank deposits</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3 mb-md-0">
            <div class="summary-card">
                <div class="summary-icon icon-outstanding">
                    <i class="fa fa-money-bill-wave"></i>
                </div>
                <div class="summary-content">
                    <div class="summary-value">KSh {{ number_format($summary['open_deposit_total'], 2) }}</div>
                    <div class="summary-label">Unmatched deposit total</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3 mb-md-0">
            <div class="summary-card">
                <div class="summary-icon icon-debtors">
                    <i class="fa fa-receipt"></i>
                </div>
                <div class="summary-content">
                    <div class="summary-value">{{ $summary['unmatched_inflows'] }}</div>
                    <div class="summary-label">Unmatched payments</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-card">
                <div class="summary-icon icon-overdue">
                    <i class="fa fa-coins"></i>
                </div>
                <div class="summary-content">
                    <div class="summary-value">KSh {{ number_format($summary['unmatched_inflow_total'], 2) }}</div>
                    <div class="summary-label">Unmatched payment total</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Record Deposit --}}
    <div class="card form-card mb-4">
        <div class="card-header"><h5 class="mb-0"><i class="fa fa-university me-2"></i>Record bank deposit</h5></div>
        <div class="card-body">
            <form action="{{ route('reconciliation.deposits.store') }}" method="POST" class="row g-3 align-items-end">
                @csrf
                <div class="col-md-2">
                    <label class="form-label">Date</label>
                    <input type="date" name="deposit_date" class="form-control" value="{{ old('deposit_date', now()->toDateString()) }}" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Amount (KSh)</label>
                    <input type="number" name="amount" class="form-control" step="0.01" min="0.01" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Bank reference</label>
                    <input type="text" name="reference" class="form-control" placeholder="e.g. FT260618001" maxlength="100">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Description</label>
                    <input type="text" name="description" class="form-control" placeholder="e.g. Paybill batch / bank transfer" maxlength="255">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="fa fa-plus me-1"></i>Add deposit</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit Selected Deposit --}}
    @if($selectedDeposit)
    <div class="card form-card form-card-highlight mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fa fa-pen me-2"></i>Edit selected deposit</h5>
            <form action="{{ route('reconciliation.deposits.destroy', $selectedDeposit) }}" method="POST"
                  onsubmit="return confirm('Delete this bank deposit? Matched payments will be unmatched again. Fee payments in the cashbook are not removed.');">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fa fa-trash me-1"></i>Delete deposit</button>
            </form>
        </div>
        <div class="card-body">
            <form action="{{ route('reconciliation.deposits.update', $selectedDeposit) }}" method="POST" class="row g-3 align-items-end">
                @csrf @method('PUT')
                <input type="hidden" name="deposit_id" value="{{ $selectedDeposit->id }}">
                <div class="col-md-2">
                    <label class="form-label">Date</label>
                    <input type="date" name="deposit_date" class="form-control" value="{{ $selectedDeposit->deposit_date->toDateString() }}" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Amount (KSh)</label>
                    <input type="number" name="amount" class="form-control" step="0.01" min="0.01"
                           value="{{ $selectedDeposit->amount }}" required>
                    @if($selectedDeposit->matchedAmount() > 0)
                        <div class="form-text">Min {{ number_format($selectedDeposit->matchedAmount(), 2) }} (already matched)</div>
                    @endif
                </div>
                <div class="col-md-2">
                    <label class="form-label">Bank reference</label>
                    <input type="text" name="reference" class="form-control" value="{{ $selectedDeposit->reference }}" maxlength="100">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Description</label>
                    <input type="text" name="description" class="form-control" value="{{ $selectedDeposit->description }}" maxlength="255">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Save changes</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <div class="row">
        {{-- Bank Deposits --}}
        <div class="col-xl-5 mb-4">
            <div class="card form-card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Bank deposits</h5>
                    <form method="GET" class="d-flex gap-2">
                        <input type="hidden" name="from" value="{{ $from->toDateString() }}">
                        <input type="hidden" name="to" value="{{ $to->toDateString() }}">
                        @if($method)<input type="hidden" name="method" value="{{ $method }}">@endif
                        <select name="deposit_status" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="open" {{ request('deposit_status', 'open') === 'open' ? 'selected' : '' }}>Open</option>
                            <option value="reconciled" {{ request('deposit_status') === 'reconciled' ? 'selected' : '' }}>Reconciled</option>
                            <option value="all" {{ request('deposit_status') === 'all' ? 'selected' : '' }}>All</option>
                        </select>
                    </form>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table report-table report-table-compact mb-0">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Reference</th>
                                    <th class="text-end">Amount</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($deposits as $deposit)
                                    @php
                                        $isSelected = $selectedDeposit && $selectedDeposit->id === $deposit->id;
                                    @endphp
                                    <tr class="{{ $isSelected ? 'row-selected' : '' }}">
                                        <td>{{ $deposit->deposit_date->format('d M Y') }}</td>
                                        <td>
                                            <div>{{ $deposit->reference ?: '—' }}</div>
                                            @if($deposit->description)
                                                <small class="text-muted">{{ Str::limit($deposit->description, 30) }}</small>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            KSh {{ number_format($deposit->amount, 2) }}
                                            @if($deposit->status !== 'reconciled')
                                                <div><small class="text-muted">Left: {{ number_format($deposit->remainingAmount(), 2) }}</small></div>
                                            @endif
                                        </td>
                                        <td>
                                            @if($deposit->status === 'reconciled')
                                                <span class="badge badge-good">Reconciled</span>
                                            @elseif($deposit->status === 'partial')
                                                <span class="badge badge-watch">Partial</span>
                                            @else
                                                <span class="badge badge-neutral">Unmatched</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('reconciliation.index', array_merge(request()->query(), ['deposit_id' => $deposit->id])) }}" class="btn btn-sm btn-outline-primary">Select</a>
                                        </td>
                                    </tr>
                                    @if($deposit->matches->isNotEmpty())
                                        @foreach($deposit->matches as $m)
                                            <tr class="row-match">
                                                <td colspan="2">
                                                    <small class="text-muted">
                                                        <i class="fa fa-link me-1"></i>
                                                        {{ $m->cashbookEntry?->description }}
                                                    </small>
                                                </td>
                                                <td class="text-end"><small>KSh {{ number_format($m->amount, 2) }}</small></td>
                                                <td colspan="2">
                                                    <form action="{{ route('reconciliation.unmatch', $m) }}" method="POST" class="d-inline"
                                                          onsubmit="return confirm('Remove this match? The payment will appear as unmatched again.');">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-link text-danger p-0">Unmatch</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                @empty
                                    <tr><td colspan="5" class="text-center py-4 text-muted">No bank deposits yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($deposits->hasPages())
                    <div class="card-footer">{{ $deposits->links() }}</div>
                @endif
            </div>
        </div>

        {{-- Unmatched Fee Payments --}}
        <div class="col-xl-7 mb-4">
            <div class="card form-card h-100">
                <div class="card-header">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <h5 class="mb-0">Unmatched fee payments (cashbook)</h5>
                        <form method="GET" class="d-flex flex-wrap gap-2">
                            @if($selectedDeposit)
                                <input type="hidden" name="deposit_id" value="{{ $selectedDeposit->id }}">
                            @endif
                            <input type="date" name="from" class="form-control form-control-sm" value="{{ $from->toDateString() }}">
                            <input type="date" name="to" class="form-control form-control-sm" value="{{ $to->toDateString() }}">
                            <select name="method" class="form-select form-select-sm">
                                <option value="">All methods</option>
                                @foreach(['Bank', 'Mpesa', 'Cash', 'Cheque'] as $m)
                                    <option value="{{ $m }}" {{ $method === $m ? 'selected' : '' }}>{{ $m }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-sm btn-outline-secondary">Filter</button>
                        </form>
                    </div>
                </div>
                @if($selectedDeposit)
                    <div class="context-bar">
                        <small>
                            <strong>Matching to:</strong>
                            {{ $selectedDeposit->deposit_date->format('d M Y') }} —
                            KSh {{ number_format($selectedDeposit->amount, 2) }}
                            (remaining KSh {{ number_format($selectedDeposit->remainingAmount(), 2) }})
                            @if($selectedDeposit->reference) · Ref: {{ $selectedDeposit->reference }} @endif
                        </small>
                    </div>
                @else
                    <div class="context-bar context-bar-warn">
                        <small class="text-muted">Select an open bank deposit on the left to start matching payments.</small>
                    </div>
                @endif
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table report-table report-table-compact mb-0">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Student / Description</th>
                                    <th>Method</th>
                                    <th class="text-end">Amount</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($unmatchedInflows as $entry)
                                    @php
                                        $payment = $entry->source;
                                        $student = $payment?->invoice?->student;
                                        $score = $suggestions->get($entry->id)['score'] ?? 0;
                                    @endphp
                                    <tr class="{{ $score >= 100 ? 'row-suggested-strong' : ($score >= 50 ? 'row-suggested-weak' : '') }}">
                                        <td>{{ \Carbon\Carbon::parse($entry->transaction_date)->format('d M Y') }}</td>
                                        <td>
                                            @if($student)
                                                <div>{{ $student->full_name }}</div>
                                                <small class="text-muted">{{ $student->admission }}</small>
                                            @else
                                                {{ Str::limit($entry->description, 45) }}
                                            @endif
                                            @if($score >= 100)
                                                <span class="badge badge-good badge-sm ms-1">Likely match</span>
                                            @endif
                                        </td>
                                        <td>{{ ucfirst($entry->payment_method) }}</td>
                                        <td class="text-end">KSh {{ number_format($entry->amount, 2) }}</td>
                                        <td>
                                            @if($selectedDeposit && $selectedDeposit->remainingAmount() > 0)
                                                <form action="{{ route('reconciliation.match') }}" method="POST"
                                                      onsubmit="return confirm('Link this payment to the selected bank deposit?');">
                                                    @csrf
                                                    <input type="hidden" name="bank_deposit_id" value="{{ $selectedDeposit->id }}">
                                                    <input type="hidden" name="cashbook_entry_id" value="{{ $entry->id }}">
                                                    <button type="submit" class="btn btn-sm btn-success">Match</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center py-4 text-muted">No unmatched payment inflows in this period.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Base Variables — shared across all report & form views */
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

/* Alerts */
.alert {
    border-radius: var(--border-radius);
    border: none;
    padding: 16px;
}

.alert-success {
    background-color: #e8f5e1;
    color: var(--success-dark);
}

.alert-danger {
    background-color: #fee2e2;
    color: #991b1b;
}

/* Help Callout */
.info-callout {
    background: var(--gray-50);
    border: 1px solid var(--gray-200);
    border-radius: var(--border-radius);
    padding: 16px 20px;
    color: var(--gray-700);
}

.info-callout i {
    color: var(--primary-color);
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

.icon-budget {
    background: rgba(54, 169, 226, 0.1);
    color: var(--primary-color);
}

.icon-outstanding {
    background: rgba(245, 158, 11, 0.12);
    color: var(--warning-color);
}

.icon-debtors {
    background: rgba(239, 68, 68, 0.1);
    color: var(--danger-color);
}

.icon-overdue {
    background: rgba(220, 38, 38, 0.1);
    color: #dc2626;
}

.summary-value {
    font-size: 20px;
    font-weight: 700;
    color: var(--gray-900);
    line-height: 1.2;
}

.summary-label {
    font-size: 13px;
    color: var(--gray-500);
    margin-top: 2px;
}

/* Cards */
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

.form-card .card-footer {
    background: var(--gray-50);
    border-top: 1px solid var(--gray-200);
    padding: 12px 24px;
}

.form-card-highlight {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(54, 169, 226, 0.1);
}

/* Inputs inside form-card forms */
.form-card .form-label {
    font-size: 13px;
    font-weight: 500;
    color: var(--gray-700);
    margin-bottom: 6px;
}

.form-card .form-control,
.form-card .form-select {
    border: 1px solid var(--gray-300);
    border-radius: var(--border-radius);
    font-size: 14px;
}

.form-card .form-control:focus,
.form-card .form-select:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(54, 169, 226, 0.1);
}

/* Buttons */
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

/* Badges */
.badge-good,
.badge-watch,
.badge-neutral {
    font-size: 13px;
    font-weight: 500;
    padding: 6px 12px;
    border-radius: var(--border-radius);
}

.badge-good {
    background-color: #e8f5e1;
    color: var(--success-dark);
}

.badge-watch {
    background-color: #fef3c7;
    color: #92400e;
}

.badge-neutral {
    background-color: var(--gray-100);
    color: var(--gray-700);
}

.badge-sm {
    font-size: 11px;
    padding: 3px 8px;
}

/* Context Bars */
.context-bar {
    padding: 10px 24px;
    border-bottom: 1px solid var(--gray-200);
    background: rgba(54, 169, 226, 0.06);
    color: var(--gray-700);
}

.context-bar-warn {
    background: rgba(245, 158, 11, 0.1);
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
    vertical-align: middle;
}

.report-table-compact thead th,
.report-table-compact td {
    padding: 8px 16px;
}

/* Row Highlights */
.row-selected td {
    background: rgba(54, 169, 226, 0.08);
}

.row-match td {
    background: var(--gray-50);
    border-top: none;
}

.row-suggested-strong td {
    background: rgba(121, 195, 71, 0.1);
}

.row-suggested-weak td {
    background: rgba(245, 158, 11, 0.1);
}

/* Responsive Design */
@media (max-width: 768px) {
    .page-header h4 {
        font-size: 20px;
    }

    .summary-card {
        padding: 16px;
    }

    .summary-value {
        font-size: 18px;
    }

    .report-table thead th,
    .report-table td,
    .report-table-compact thead th,
    .report-table-compact td {
        padding: 8px 12px;
    }

    .context-bar {
        padding: 10px 16px;
    }
}
</style>
@endsection