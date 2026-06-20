@extends('layouts.app')
@section('main')

<div class="main-wrapper">
    <div class="page-header mb-4">
        <h4 class="mb-1">Fee Waivers & Bursaries</h4>
        <p class="text-muted mb-0">Review waiver requests and approval trail</p>
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

    <div class="card filter-card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('waivers.index') }}" class="d-flex flex-wrap gap-3 align-items-end">
                <div>
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="pending" {{ request('status', 'pending') === 'pending' ? 'selected' : '' }}>Pending ({{ $pendingCount }})</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>All</option>
                    </select>
                </div>
                <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary">
                    <i class="fa fa-arrow-left me-1"></i>Back to Invoices
                </a>
            </form>
        </div>
    </div>

    <div class="card table-card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fa fa-hand-holding-usd me-2"></i>Waiver Requests</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Requested</th>
                            <th>Student</th>
                            <th>Scope</th>
                            <th>Discount</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Trail</th>
                            @if(auth()->user()->role === 'admin')
                                <th></th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($waivers as $waiver)
                            <tr>
                                <td>
                                    <div>{{ $waiver->requested_at?->format('d M Y H:i') }}</div>
                                    <small class="text-muted">{{ $waiver->requestedBy?->admin_name }}</small>
                                </td>
                                <td>
                                    <div>{{ $waiver->invoice?->student?->full_name ?? '—' }}</div>
                                    <small class="text-muted">{{ $waiver->invoice?->student?->admission }}</small>
                                </td>
                                <td>
                                    @if($waiver->scope === 'line')
                                        Line: {{ Str::limit($waiver->target_description, 30) }}
                                    @else
                                        Whole invoice
                                    @endif
                                </td>
                                <td>
                                    @if($waiver->discount_type === 'percentage')
                                        {{ number_format($waiver->value, 2) }}%
                                    @else
                                        KSh {{ number_format($waiver->value, 2) }}
                                    @endif
                                    @if($waiver->computed_amount)
                                        <div><small class="text-success">Applied: KSh {{ number_format($waiver->computed_amount, 2) }}</small></div>
                                    @endif
                                </td>
                                <td><small title="{{ $waiver->reason }}">{{ Str::limit($waiver->reason, 50) }}</small></td>
                                <td>
                                    @if($waiver->status === 'pending')
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @elseif($waiver->status === 'approved')
                                        <span class="badge bg-success">Approved</span>
                                    @else
                                        <span class="badge bg-secondary">Rejected</span>
                                    @endif
                                </td>
                                <td>
                                    @if($waiver->reviewed_at)
                                        <small>
                                            {{ $waiver->reviewed_at->format('d M Y H:i') }}<br>
                                            {{ $waiver->reviewedBy?->admin_name }}
                                            @if($waiver->review_notes)
                                                <br><span class="text-muted" title="{{ $waiver->review_notes }}">{{ Str::limit($waiver->review_notes, 40) }}</span>
                                            @endif
                                        </small>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                @if(auth()->user()->role === 'admin')
                                    <td class="text-end" style="min-width: 220px;">
                                        @if($waiver->isPending())
                                            <form action="{{ route('waivers.approve', $waiver) }}" method="POST" class="mb-2">
                                                @csrf
                                                <input type="text" name="review_notes" class="form-control form-control-sm mb-1" placeholder="Approval notes (optional)" maxlength="500">
                                                <button type="submit" class="btn btn-sm btn-success w-100">Approve</button>
                                            </form>
                                            <form action="{{ route('waivers.reject', $waiver) }}" method="POST">
                                                @csrf
                                                <input type="text" name="review_notes" class="form-control form-control-sm mb-1" placeholder="Rejection reason (required)" maxlength="500" required>
                                                <button type="submit" class="btn btn-sm btn-outline-danger w-100">Reject</button>
                                            </form>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ auth()->user()->role === 'admin' ? 8 : 7 }}" class="text-center py-4 text-muted">
                                    No waiver requests found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($waivers->hasPages())
            <div class="card-footer">{{ $waivers->links() }}</div>
        @endif
    </div>
</div>

<style>
.filter-card, .table-card { border: 1px solid #e5e7eb; border-radius: 8px; }
.table-card .card-header { background: #f9fafb; border-bottom: 1px solid #e5e7eb; padding: 16px 20px; }
.table thead { background: #f9fafb; }
.table th, .table td { padding: 12px 16px; vertical-align: top; font-size: 14px; }
</style>
@endsection
