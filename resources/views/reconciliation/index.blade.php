@extends('layouts.app')
@section('main')

<div class="main-wrapper">
    <div class="page-header mb-4">
        <h4 class="mb-1">Bank Reconciliation</h4>
        <p class="text-muted mb-0">Match bank statement deposits to fee payments recorded in the cashbook</p>
    </div>

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

    <div class="alert alert-light border mb-4 py-3">
        <strong><i class="fa fa-info-circle me-1"></i>Made a mistake?</strong>
        <ul class="mb-0 mt-2 small text-muted">
            <li><strong>Wrong match</strong> — click <em>Unmatch</em> on the linked payment under the deposit. The payment returns to the unmatched list; the deposit opens again.</li>
            <li><strong>Wrong deposit (typo in amount/date/ref)</strong> — select the deposit and use <em>Edit deposit</em> below.</li>
            <li><strong>Deposit entered by mistake</strong> — select it and click <em>Delete deposit</em>. This removes all links; payments are not deleted, only unmatched.</li>
        </ul>
    </div>


    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-value">{{ $summary['open_deposits'] }}</div>
                <div class="stat-label">Open bank deposits</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-value">KSh {{ number_format($summary['open_deposit_total'], 2) }}</div>
                <div class="stat-label">Unmatched deposit total</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-value">{{ $summary['unmatched_inflows'] }}</div>
                <div class="stat-label">Unmatched payments</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-value">KSh {{ number_format($summary['unmatched_inflow_total'], 2) }}</div>
                <div class="stat-label">Unmatched payment total</div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
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

    @if($selectedDeposit)
    <div class="card mb-4 border-primary">
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
        <div class="col-xl-5 mb-4">
            <div class="card h-100">
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
                        <table class="table table-sm mb-0">
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
                                    <tr class="{{ $isSelected ? 'table-primary' : '' }}">
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
                                                <span class="badge bg-success">Reconciled</span>
                                            @elseif($deposit->status === 'partial')
                                                <span class="badge bg-warning text-dark">Partial</span>
                                            @else
                                                <span class="badge bg-secondary">Unmatched</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('reconciliation.index', array_merge(request()->query(), ['deposit_id' => $deposit->id])) }}" class="btn btn-sm btn-outline-primary">Select</a>
                                        </td>
                                    </tr>
                                    @if($deposit->matches->isNotEmpty())
                                        @foreach($deposit->matches as $m)
                                            <tr class="table-light">
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

        <div class="col-xl-7 mb-4">
            <div class="card h-100">
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
                    <div class="card-body border-bottom py-2 bg-light">
                        <small>
                            <strong>Matching to:</strong>
                            {{ $selectedDeposit->deposit_date->format('d M Y') }} —
                            KSh {{ number_format($selectedDeposit->amount, 2) }}
                            (remaining KSh {{ number_format($selectedDeposit->remainingAmount(), 2) }})
                            @if($selectedDeposit->reference) · Ref: {{ $selectedDeposit->reference }} @endif
                        </small>
                    </div>
                @else
                    <div class="card-body border-bottom py-2 bg-warning-subtle">
                        <small class="text-muted">Select an open bank deposit on the left to start matching payments.</small>
                    </div>
                @endif
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
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
                                    <tr class="{{ $score >= 100 ? 'table-success' : ($score >= 50 ? 'table-warning' : '') }}">
                                        <td>{{ \Carbon\Carbon::parse($entry->transaction_date)->format('d M Y') }}</td>
                                        <td>
                                            @if($student)
                                                <div>{{ $student->full_name }}</div>
                                                <small class="text-muted">{{ $student->admission }}</small>
                                            @else
                                                {{ Str::limit($entry->description, 45) }}
                                            @endif
                                            @if($score >= 100)
                                                <span class="badge bg-success ms-1">Likely match</span>
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
.stat-card { background:#fff; border:1px solid #e5e7eb; border-radius:8px; padding:16px; }
.stat-value { font-size:18px; font-weight:700; color:#111827; }
.stat-label { font-size:13px; color:#6b7280; }
.card { border:1px solid #e5e7eb; border-radius:8px; box-shadow:0 1px 3px rgba(0,0,0,.05); }
.card-header { background:#f9fafb; border-bottom:1px solid #e5e7eb; padding:14px 18px; }
.table th, .table td { padding:10px 14px; vertical-align:middle; font-size:14px; }
</style>
@endsection
