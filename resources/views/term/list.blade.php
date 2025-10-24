@extends('layouts.app')
@section('main')

<div class="main-wrapper">
    {{-- Page Header --}}
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h4 class="mb-1">Term Management</h4>
                <p class="text-muted mb-0">Manage academic terms and schedules</p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <a href="{{ route('addterm') }}" class="btn btn-success">
                    <i class="fa fa-plus me-2"></i>Add New Term
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

    {{-- Term Promotion Card --}}
    <div class="card promotion-card mb-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fa fa-arrow-circle-right me-2"></i>Term Promotion
            </h5>
            <p class="mb-0 mt-1 header-subtitle">Promote students to the next academic term</p>
        </div>

        <div class="card-body">
            @if(isset($currentTerm))
                <form action="{{ route('promotions.term') }}" method="POST" id="promotionForm">
                    @csrf
                    <input type="hidden" name="from_term_id" value="{{ $currentTerm->id }}">

                    <div class="row g-3 align-items-end">
                        <div class="col-md-5">
                            <label class="form-label">
                                <i class="fa fa-calendar-check me-1"></i>From Term
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fa fa-lock"></i>
                                </span>
                                <input type="text" 
                                    value="{{ $currentTerm->name }}" 
                                    class="form-control" 
                                    readonly>
                            </div>
                            <small class="text-muted">Current active term</small>
                        </div>

                        <div class="col-md-5">
                            <label class="form-label">
                                <i class="fa fa-calendar-plus me-1"></i>To Term
                                <span class="text-danger">*</span>
                            </label>
                            <select name="to_term_id" class="form-select" required>
                                <option value="">Select Next Term</option>
                                @foreach ($terms as $term)
                                    @if ($term->id != $currentTerm->id)
                                        <option value="{{ $term->id }}">{{ $term->name }} ({{ $term->year }})</option>
                                    @endif
                                @endforeach
                            </select>
                            <small class="text-muted">Select the term to promote students to</small>
                        </div>

                        <div class="col-md-2">
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fa fa-rocket me-2"></i>Promote
                            </button>
                        </div>
                    </div>
                </form>
            @else
                <div class="alert alert-warning">
                    <i class="fa fa-exclamation-triangle me-2"></i>
                    No current term found. Please create and set a current term before running a promotion.
                </div>
            @endif
        </div>
    </div>

    {{-- Term List Table --}}
    <div class="card table-card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fa fa-calendar-alt me-2"></i>Term List
                </h5>
                <span class="badge bg-light text-dark">{{ count($getRecord) }} Terms</span>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table term-table mb-0">
                    <thead>
                        <tr>
                            <th style="width: 60px;">ID</th>
                            <th>Term</th>
                            <th>Year</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($getRecord as $value)
                            <tr>
                                <td>
                                    <span class="row-number">#{{ $value->id }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="term-icon">
                                            {{ strtoupper(substr($value->name, 0, 1)) }}
                                        </div>
                                        <div class="ms-3">
                                            <div class="term-name">{{ $value->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="year-badge">{{ $value->year }}</span>
                                </td>
                                <td>
                                    <span class="date-text">
                                        <i class="fa fa-calendar-day me-1"></i>
                                        {{ \Carbon\Carbon::parse($value->start_date)->format('d M, Y') }}
                                    </span>
                                </td>
                                <td>
                                    @if($value->end_date)
                                        <span class="date-text">
                                            <i class="fa fa-calendar-check me-1"></i>
                                            {{ \Carbon\Carbon::parse($value->end_date)->format('d M, Y') }}
                                        </span>
                                    @else
                                        <span class="text-muted">
                                            <i class="fa fa-minus me-1"></i>Not Set
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($value->active)
                                        <span class="badge badge-success">
                                            <i class="fa fa-check-circle me-1"></i>Active
                                        </span>
                                    @else
                                        <span class="badge-method">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ url('/editterm/' . $value->id) }}" 
                                           class="btn btn-sm btn-light" 
                                           title="Edit Term">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <a href="{{ url('/deleteterm/' . $value->id) }}" 
                                           class="btn btn-sm btn-danger" 
                                           onclick="return confirm('Are you sure you want to delete this term?')" 
                                           title="Delete Term">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="fa fa-inbox fa-3x mb-3"></i>
                                        <p class="mb-2">No terms available</p>
                                        <small class="d-block mb-3">Click "Add New Term" to create one</small>
                                        <a href="{{ route('addterm') }}" class="btn btn-success">
                                            <i class="fa fa-plus me-2"></i>Add New Term
                                        </a>
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
.promotion-card,
.table-card {
    border: 1px solid var(--gray-200);
    border-radius: var(--border-radius);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.promotion-card .card-header,
.table-card .card-header {
    background: var(--gray-50);
    border-bottom: 1px solid var(--gray-200);
    padding: 16px 20px;
}

.promotion-card .card-header h5,
.table-card .card-header h5 {
    font-size: 16px;
    font-weight: 600;
    color: var(--gray-900);
}

.header-subtitle {
    font-size: 13px;
    color: var(--gray-500);
    font-weight: 400;
}

.promotion-card .card-body {
    padding: 24px;
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

.form-control[readonly] {
    background-color: var(--gray-50);
    color: var(--gray-600);
}

.text-muted {
    font-size: 12px;
    margin-top: 4px;
    display: block;
}

/* Input Group */
.input-group-text {
    background-color: var(--gray-50);
    border: 1px solid var(--gray-300);
    border-right: none;
    color: var(--gray-600);
}

.input-group .form-control {
    border-left: none;
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

.btn-light {
    background-color: var(--gray-100);
    border-color: var(--gray-200);
    color: var(--gray-700);
}

.btn-light:hover {
    background-color: var(--gray-200);
    border-color: var(--gray-300);
}

/* Table */
.term-table {
    font-size: 14px;
}

.term-table thead {
    background-color: var(--gray-50);
    border-bottom: 2px solid var(--gray-200);
}

.term-table thead th {
    font-weight: 600;
    color: var(--gray-700);
    padding: 12px 16px;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.term-table tbody td {
    padding: 16px;
    vertical-align: middle;
    border-bottom: 1px solid var(--gray-100);
}

.term-table tbody tr:hover {
    background-color: var(--gray-50);
}

/* Row Number */
.row-number {
    color: var(--gray-500);
    font-weight: 500;
    font-size: 13px;
}

/* Term Icon */
.term-icon {
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

.term-name {
    font-weight: 500;
    color: var(--gray-900);
}

/* Year Badge */
.year-badge {
    background-color: var(--gray-100);
    color: var(--gray-700);
    padding: 4px 10px;
    border-radius: 6px;
    font-weight: 500;
    font-size: 12px;
    display: inline-block;
}

/* Date */
.date-text {
    color: var(--gray-600);
    font-size: 13px;
}

.date-text i {
    color: var(--gray-400);
}

/* Badges */
.badge {
    padding: 4px 10px;
    border-radius: 6px;
    font-weight: 500;
    font-size: 12px;
}

.badge-success {
    background-color: #e8f5e0;
    color: #3d7a1f;
}

.badge-method {
    background-color: var(--gray-100);
    color: var(--gray-700);
    padding: 4px 10px;
    border-radius: 6px;
    font-weight: 500;
    font-size: 12px;
    display: inline-block;
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

.alert-warning {
    background-color: #fef3c7;
    color: #92400e;
}

/* Badge Override */
.badge.bg-light {
    background-color: var(--gray-100) !important;
    color: var(--gray-700);
    padding: 4px 12px;
    font-weight: 500;
}

/* Responsive Design */
@media (max-width: 768px) {
    .term-icon {
        width: 32px;
        height: 32px;
        font-size: 14px;
    }
    
    .action-buttons .btn-sm {
        padding: 6px 10px;
        font-size: 12px;
    }
    
    .term-table {
        font-size: 13px;
    }

    .term-table thead th,
    .term-table tbody td {
        padding: 10px;
    }
}
</style>

@endsection