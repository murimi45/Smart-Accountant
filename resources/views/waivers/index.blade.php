@extends('layouts.app')
@section('main')

<div class="main-wrapper">
    {{-- Page Header --}}
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h4 class="mb-1">Fee Waivers & Bursaries</h4>
                <p class="text-muted mb-0">Review waiver requests and approval trail</p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary">
                    <i class="fa fa-arrow-left me-2"></i>Back to Invoices
                </a>
            </div>
        </div>
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

    {{-- Search Filters Card --}}
    <div class="card filter-card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('waivers.index') }}" class="filter-form">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label"><i class="fa fa-filter me-1"></i>Status</label>
                        <select name="status" class="form-select" onchange="this.form.submit()">
                            <option value="pending" {{ request('status', 'pending') === 'pending' ? 'selected' : '' }}>Pending ({{ $pendingCount }})</option>
                            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                            <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>All</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Waiver List Table --}}
    <div class="card table-card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fa fa-hand-holding-usd me-2"></i>Waiver Requests
                </h5>
                <span class="badge bg-light text-dark">{{ $waivers->total() }} Requests</span>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table admin-table mb-0">
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
                                <th>Action</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($waivers as $waiver)
                            <tr>
                                <td>
                                    <div class="admin-name">{{ $waiver->requested_at?->format('d M Y H:i') }}</div>
                                    <span class="email-text">{{ $waiver->requestedBy?->admin_name }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="admin-avatar">
                                            {{ strtoupper(substr($waiver->invoice?->student?->full_name ?? '-', 0, 1)) }}
                                        </div>
                                        <div class="ms-3">
                                            <div class="admin-name">{{ $waiver->invoice?->student?->full_name ?? '—' }}</div>
                                            <span class="email-text">{{ $waiver->invoice?->student?->admission }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($waiver->scope === 'line')
                                        <span class="role-badge">Line: {{ Str::limit($waiver->target_description, 30) }}</span>
                                    @else
                                        <span class="role-badge">Whole invoice</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="admin-name">
                                        @if($waiver->discount_type === 'percentage')
                                            {{ number_format($waiver->value, 2) }}%
                                        @else
                                            KSh {{ number_format($waiver->value, 2) }}
                                        @endif
                                    </div>
                                    @if($waiver->computed_amount)
                                        <span class="applied-amount">Applied: KSh {{ number_format($waiver->computed_amount, 2) }}</span>
                                    @endif
                                </td>
                                <td><span class="email-text" title="{{ $waiver->reason }}">{{ Str::limit($waiver->reason, 50) }}</span></td>
                                <td>
                                    @if($waiver->status === 'pending')
                                        <span class="badge status-pending">Pending</span>
                                    @elseif($waiver->status === 'approved')
                                        <span class="badge status-approved">Approved</span>
                                    @else
                                        <span class="badge status-rejected">Rejected</span>
                                    @endif
                                </td>
                                <td>
                                    @if($waiver->reviewed_at)
                                        <div class="admin-name">{{ $waiver->reviewed_at->format('d M Y H:i') }}</div>
                                        <span class="email-text">{{ $waiver->reviewedBy?->admin_name }}</span>
                                        @if($waiver->review_notes)
                                            <div class="email-text" title="{{ $waiver->review_notes }}">{{ Str::limit($waiver->review_notes, 40) }}</div>
                                        @endif
                                    @else
                                        <span class="email-text">—</span>
                                    @endif
                                </td>
                                @if(auth()->user()->role === 'admin')
                                    <td style="min-width: 220px;">
                                        @if($waiver->isPending())
                                            <div class="action-buttons flex-column align-items-stretch">
                                                <form action="{{ route('waivers.approve', $waiver) }}" method="POST" class="mb-2">
                                                    @csrf
                                                    <input type="text" name="review_notes" class="form-control form-control-sm mb-2" placeholder="Approval notes (optional)" maxlength="500">
                                                    <button type="submit" class="btn btn-sm btn-success w-100">
                                                        <i class="fa fa-check me-1"></i>Approve
                                                    </button>
                                                </form>
                                                <form action="{{ route('waivers.reject', $waiver) }}" method="POST">
                                                    @csrf
                                                    <input type="text" name="review_notes" class="form-control form-control-sm mb-2" placeholder="Rejection reason (required)" maxlength="500" required>
                                                    <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                                                        <i class="fa fa-times me-1"></i>Reject
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ auth()->user()->role === 'admin' ? 8 : 7 }}" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="fa fa-inbox fa-3x mb-3"></i>
                                        <p class="mb-2">No waiver requests found</p>
                                        <small class="d-block mb-3">Try adjusting your status filter</small>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        @if($waivers->hasPages())
        <div class="card-footer">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div class="mb-2 mb-sm-0">
                    <small class="text-muted">
                        Showing {{ $waivers->firstItem() }} to {{ $waivers->lastItem() }} of {{ $waivers->total() }} entries
                    </small>
                </div>
                <div>
                    {{ $waivers->links() }}
                </div>
            </div>
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

.btn-danger {
    background-color: var(--danger-color);
    border-color: var(--danger-color);
}

.btn-danger:hover {
    background-color: #dc2626;
    border-color: #dc2626;
}

.btn-outline-danger {
    color: var(--danger-color);
    border-color: var(--danger-color);
    background: white;
}

.btn-outline-danger:hover {
    background-color: var(--danger-color);
    border-color: var(--danger-color);
    color: white;
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

.btn-light {
    background-color: var(--gray-100);
    border-color: var(--gray-200);
    color: var(--gray-700);
}

.btn-light:hover {
    background-color: var(--gray-200);
    border-color: var(--gray-300);
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
    vertical-align: top;
    border-bottom: 1px solid var(--gray-100);
}

.admin-table tbody tr:hover {
    background-color: var(--gray-50);
}

/* Avatar */
.admin-avatar {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    background-color: var(--gray-100);
    color: var(--gray-700);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 16px;
    flex-shrink: 0;
}

.admin-name {
    font-weight: 500;
    color: var(--gray-900);
}

/* Text Styles */
.email-text {
    color: var(--gray-600);
    font-size: 13px;
    display: block;
}

.applied-amount {
    color: var(--success-dark);
    font-size: 12px;
    display: block;
    margin-top: 4px;
    font-weight: 500;
}

/* Scope / Role-style Badge */
.role-badge {
    background-color: var(--gray-100);
    color: var(--gray-700);
    padding: 4px 10px;
    border-radius: 6px;
    font-weight: 500;
    font-size: 12px;
    display: inline-block;
}

/* Status Badges */
.status-pending {
    background-color: #fef3c7;
    color: #92400e;
    font-weight: 500;
    padding: 5px 10px;
    border-radius: 6px;
}

.status-approved {
    background-color: #e8f5e0;
    color: #3d7a1f;
    font-weight: 500;
    padding: 5px 10px;
    border-radius: 6px;
}

.status-rejected {
    background-color: var(--gray-100);
    color: var(--gray-600);
    font-weight: 500;
    padding: 5px 10px;
    border-radius: 6px;
}

/* Action Buttons */
.action-buttons {
    display: flex;
    gap: 6px;
}

.action-buttons.flex-column {
    gap: 0;
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
    .admin-avatar {
        width: 32px;
        height: 32px;
        font-size: 14px;
    }

    .btn-sm {
        padding: 6px 10px;
        font-size: 12px;
    }

    .admin-table {
        font-size: 13px;
    }

    .admin-table thead th,
    .admin-table tbody td {
        padding: 10px;
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