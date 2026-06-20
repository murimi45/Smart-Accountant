@extends('layouts.app')

@section('main')
<div class="main-wrapper">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h4 class="mb-1">General Ledger</h4>
                <p class="text-muted mb-0">Double-entry lines posted automatically from cashbook transactions</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="{{ route('accounts.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fa fa-list me-1"></i>Chart of Accounts
                </a>
            </div>
        </div>
    </div>

    <div class="card filter-card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('ledger.index') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Account</label>
                    <select name="account_id" class="form-select">
                        <option value="">All accounts</option>
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}" {{ (string) $accountId === (string) $account->id ? 'selected' : '' }}>
                                {{ $account->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">From</label>
                    <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">To</label>
                    <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary me-2">Filter</button>
                    <a href="{{ route('ledger.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="row mb-3">
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

    <div class="card table-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fa fa-book me-2"></i>Ledger Entries</h5>
            <span class="badge bg-light text-dark">{{ $entries->total() }} lines</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
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
                                <td>{{ $entry->transaction_date?->format('d M Y') }}</td>
                                <td>{{ $entry->account?->name }}</td>
                                <td>{{ $entry->description }}</td>
                                <td class="text-end">
                                    @if((float) $entry->debit > 0)
                                        {{ number_format($entry->debit, 2) }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if((float) $entry->credit > 0)
                                        {{ number_format($entry->credit, 2) }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    No ledger entries yet. Postings are created when cashbook transactions are recorded.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($entries->hasPages())
            <div class="card-footer">
                {{ $entries->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
