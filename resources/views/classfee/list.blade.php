```blade
@extends('layouts.app')
@section('main')

<div class="main-wrapper">
    {{-- Page Header --}}
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h4 class="mb-1">Fee Management</h4>
                <p class="text-muted mb-0">Manage class fees and payment structures</p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <a href="{{ route('addclassfee') }}" class="btn btn-success">
                    <i class="fa fa-plus me-2"></i>Add New Fee
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

    {{-- Fee Amount List Table --}}
    <div class="card table-card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fa fa-money-bill-wave me-2"></i>Fee Amount List
                </h5>
                <span class="badge bg-light text-dark">{{ count($classFees) }} Fees</span>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table fee-table mb-0">
                    <thead>
                        <tr>
                            <th>Grade</th>
                            <th>Amount</th>
                            <th>Term</th>
                            <th>Year</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($classFees as $value)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="grade-icon">
                                        {{ strtoupper(substr($value->class->name, 0, 1)) }}
                                    </div>
                                    <div class="ms-3">
                                        <div class="grade-name">{{ $value->class->name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="amount-text text-success">
                                    KSh {{ number_format($value->amount, 2) }}
                                </span>
                            </td>
                            <td>
                                <span class="term-badge">{{ $value->term->name }}</span>
                            </td>
                            <td>
                                <span class="year-text">{{ $value->year }}</span>
                            </td>
                            <td>
                                @if($value->description)
                                    <span class="description-text">{{ Str::limit($value->description, 40) }}</span>
                                @else
                                    <span class="text-muted description-empty">
                                        <i class="fa fa-minus me-1"></i>No description
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if(strtolower($value->status) == 'active')
                                    <span class="badge badge-success">
                                        <i class="fa fa-check-circle me-1"></i>Active
                                    </span>
                                @else
                                    <span class="badge-method">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ url('/editclassfee/' . $value->id) }}" 
                                       class="btn btn-sm btn-light" 
                                       title="Edit Fee">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <a href="{{ url('/deleteclassfee/' . $value->id) }}" 
                                       class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Are you sure you want to delete this fee?')" 
                                       title="Delete Fee">
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
                                    <p class="mb-2">No fees configured</p>
                                    <small class="d-block mb-3">Click "Add New Fee" to create one</small>
                                    <a href="{{ route('addclassfee') }}" class="btn btn-success">
                                        <i class="fa fa-plus me-2"></i>Add New Fee
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

/* Card */
.table-card {
    border: 1px solid var(--gray-200);
    border-radius: var(--border-radius);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.table-card .card-header {
    background: var(--gray-50);
    border-bottom: 1px solid var(--gray-200);
    padding: 16px 20px;
}

/* Table */
.fee-table {
    font-size: 14px;
}

.fee-table thead {
    background-color: var(--gray-50);
    border-bottom: 2px solid var(--gray-200);
}

.fee-table thead th {
    font-weight: 600;
    color: var(--gray-700);
    padding: 12px 16px;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.fee-table tbody td {
    padding: 16px;
    vertical-align: middle;
    border-bottom: 1px solid var(--gray-100);
}

.fee-table tbody tr:hover {
    background-color: var(--gray-50);
}

/* Grade Icon */
.grade-icon {
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

.grade-name {
    font-weight: 500;
    color: var(--gray-900);
}

/* Amount */
.amount-text {
    font-weight: 600;
    font-size: 15px;
}

.amount-text.text-success {
    color: var(--success-color) !important;
}

/* Term Badge */
.term-badge {
    background-color: var(--gray-100);
    color: var(--gray-700);
    padding: 4px 10px;
    border-radius: 6px;
    font-weight: 500;
    font-size: 12px;
    display: inline-block;
}

/* Year */
.year-text {
    color: var(--gray-600);
    font-weight: 500;
    font-size: 13px;
}

/* Description */
.description-text {
    color: var(--gray-700);
    font-size: 13px;
}

.description-empty {
    font-size: 13px;
    font-style: italic;
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

/* Responsive Design */
@media (max-width: 768px) {
    .grade-icon {
        width: 32px;
        height: 32px;
        font-size: 14px;
    }
    
    .action-buttons .btn-sm {
        padding: 6px 10px;
        font-size: 12px;
    }
    
    .fee-table {
        font-size: 13px;
    }

    .fee-table thead th,
    .fee-table tbody td {
        padding: 10px;
    }
}
</style>

@endsection
