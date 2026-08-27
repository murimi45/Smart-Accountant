@extends('layouts.app')
@section('main')

<div class="main-wrapper">
    {{-- Page Header --}}
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h4 class="mb-1">Extra Fee Assignments</h4>
                <p class="text-muted mb-0">Manage student extra fee assignments</p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <a href="{{ route('assignextrafee') }}" class="btn btn-success">
                    <i class="fa fa-plus me-2"></i>Assign Extra Fee
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
            <form method="GET" action="{{ route('listextrafeestudents') }}" class="filter-form">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="extra_fee_id" class="form-label"><i class="fa fa-receipt me-1"></i>Extra Fee</label>
                        <select name="extra_fee_id" id="extra_fee_id" class="form-select">
                            <option value="">All Extra Fees</option>
                            @foreach($extraFees as $extraFee)
                                <option value="{{ $extraFee->id }}" {{ request('extra_fee_id') == $extraFee->id ? 'selected' : '' }}>
                                    {{ $extraFee->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="student_name" class="form-label"><i class="fa fa-user me-1"></i>Search Student</label>
                        <input type="text"
                               name="student_name"
                               id="student_name"
                               value="{{ request('student_name') }}"
                               class="form-control"
                               placeholder="Name or Admission No.">
                    </div>

                    <div class="col-md-4">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="fa fa-search me-1"></i>Filter
                            </button>
                            <a href="{{ route('listextrafeestudents') }}" class="btn btn-outline-secondary" title="Reset">
                                <i class="fa fa-redo"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Assignment List Table --}}
    <div class="card table-card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fa fa-users me-2"></i>Students Assigned
                </h5>
                <span class="badge bg-light text-dark">{{ count($extraFeeStudents) }} Total</span>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table admin-table mb-0">
                    <thead>
                        <tr>
                            <th>Extra Fee</th>
                            <th>Student Name</th>
                            <th>Term</th>
                            <th>Year</th>
                            <th>Amount</th>
                            <th>Quantity</th>
                            <th>Created By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($extraFeeStudents as $extraFeeStudent)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="admin-avatar">
                                        {{ strtoupper(substr($extraFeeStudent->extraFee->name, 0, 1)) }}
                                    </div>
                                    <div class="ms-3">
                                        <div class="admin-name">{{ $extraFeeStudent->extraFee->name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @php
                                    $student = $extraFeeStudent->student;
                                    $studentName = $student?->full_name ?? 'Deleted student';
                                    $studentAdmission = $student?->admission ?? 'N/A';
                                @endphp
                                <div class="d-flex align-items-center">
                                    <div class="admin-avatar">
                                        {{ strtoupper(substr($studentName, 0, 1)) }}
                                    </div>
                                    <div class="ms-3">
                                        <div class="admin-name">{{ $studentName }}</div>
                                        <small class="text-muted">{{ $studentAdmission }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="role-badge">{{ $extraFeeStudent->extraFee->term->name }}</span>
                            </td>
                            <td>
                                <span class="year-text">{{ $extraFeeStudent->extraFee->year }}</span>
                            </td>
                            <td>
                                <span class="amount-text">KSh {{ number_format($extraFeeStudent->amount, 2) }}</span>
                            </td>
                            <td>
                                <span class="role-badge">{{ $extraFeeStudent->quantity }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="admin-avatar admin-avatar-sm">
                                        {{ strtoupper(substr($extraFeeStudent->creator->admin_name, 0, 1)) }}
                                    </div>
                                    <div class="ms-2">
                                        <span class="email-text">{{ $extraFeeStudent->creator->admin_name }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ url('/assign-extra-fee/edit/' . $extraFeeStudent->id) }}"
                                       class="btn btn-sm btn-light"
                                       title="Edit Assignment">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <a href="{{ url('/assign-extra-fee/delete/' . $extraFeeStudent->id) }}"
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Are you sure you want to delete this assignment?')"
                                       title="Delete Assignment">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fa fa-inbox fa-3x mb-3"></i>
                                    <p class="mb-2">No assignments found</p>
                                    <small class="d-block mb-3">Click "Assign Extra Fee" to create one</small>
                                    <a href="{{ route('assignextrafee') }}" class="btn btn-success">
                                        <i class="fa fa-plus me-2"></i>Assign Extra Fee
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
/* Base Variables — matched exactly to Admin Management / Extra Fee List pages */
:root {
    --primary-color: #36a9e2;
    --success-color: #79c347;
    --success-dark: #5fa732;
    --danger-color: #ef4444;
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

.btn-outline-secondary {
    color: var(--gray-600);
    border-color: var(--gray-300);
    background: white;
    padding: 8px 12px;
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

.admin-avatar-sm {
    width: 28px;
    height: 28px;
    font-size: 12px;
}

.admin-name {
    font-weight: 500;
    color: var(--gray-900);
}

/* Text Styles */
.email-text {
    color: var(--gray-600);
    font-size: 13px;
}

.amount-text {
    font-weight: 600;
    color: var(--gray-900);
    font-size: 14px;
}

.year-text {
    color: var(--gray-600);
    font-weight: 500;
    font-size: 13px;
}

/* Badge */
.role-badge {
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

/* Badge Override */
.badge.bg-light {
    background-color: var(--gray-100) !important;
    color: var(--gray-700);
    padding: 4px 12px;
    font-weight: 500;
}

/* Responsive Design */
@media (max-width: 768px) {
    .admin-avatar {
        width: 32px;
        height: 32px;
        font-size: 14px;
    }

    .admin-avatar-sm {
        width: 24px;
        height: 24px;
        font-size: 11px;
    }

    .action-buttons .btn-sm {
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
}
</style>

@endsection