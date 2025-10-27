@extends('layouts.app')
@section('main')

<div class="main-wrapper">
    {{-- Page Header --}}
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h4 class="mb-1">System User Management</h4>
                <p class="text-muted mb-0">Manage all system users</p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <a href="{{ route('admins.create') }}" class="btn btn-primary">
                    <i class="fa fa-plus me-2"></i>Add New User
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
            <form method="GET" action="{{ route('admins.index') }}" class="filter-form">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label"><i class="fa fa-user me-1"></i>Name</label>
                        <input type="text" name="name" class="form-control" placeholder="Enter name" value="{{ request('name') }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label"><i class="fa fa-envelope me-1"></i>Email</label>
                        <input type="text" name="email" class="form-control" placeholder="Enter email" value="{{ request('email') }}">
                    </div>

                    <div class="col-md-4">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="fa fa-search me-1"></i>Search
                            </button>
                            <a href="{{ route('admins.index') }}" class="btn btn-outline-secondary" title="Reset">
                                <i class="fa fa-redo"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Admin List Table --}}
    <div class="card table-card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fa fa-users me-2"></i>Users List
                </h5>
                <span class="badge bg-light text-dark">{{ $admins->total() }} Users</span>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table admin-table mb-0">
                    <thead>
                        <tr>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Role</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($admins as $admin)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="admin-avatar">
                                        {{ strtoupper(substr($admin->admin_name, 0, 1)) }}
                                    </div>
                                    <div class="ms-3">
                                        <div class="admin-name">{{ $admin->admin_name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="email-text">{{ $admin->email }}</span>
                            </td>
                            <td>
                                <span class="phone-text">{{ $admin->school->phone ?? 'N/A' }}</span>
                            </td>
                            <td>
                                <span class="role-badge">{{ ucfirst($admin->role) }}</span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('admins.edit', $admin->id) }}" 
                                       class="btn btn-sm btn-light" 
                                       title="Edit Admin">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admins.destroy', $admin->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" 
                                                onclick="return confirm('Are you sure you want to delete this admin?')" 
                                                title="Delete Admin">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fa fa-inbox fa-3x mb-3"></i>
                                    <p class="mb-2">No admins found</p>
                                    <small class="d-block mb-3">Try adjusting your search filters</small>
                                    <a href="{{ route('admins.create') }}" class="btn btn-primary">
                                        <i class="fa fa-plus me-2"></i>Add New Admin
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        @if($admins->hasPages())
        <div class="card-footer">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div class="mb-2 mb-sm-0">
                    <small class="text-muted">
                        Showing {{ $admins->firstItem() }} to {{ $admins->lastItem() }} of {{ $admins->total() }} entries
                    </small>
                </div>
                <div>
                    {{ $admins->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<style>
/* Base Variables */
:root {
    --primary-color: #36a9e2;
    --success-color: #79c347;
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

.form-control {
    border: 1px solid var(--gray-300);
    border-radius: var(--border-radius);
    padding: 8px 12px;
    font-size: 14px;
    transition: border-color 0.2s;
}

.form-control:focus {
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

/* Admin Avatar */
.admin-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
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
.email-text,
.phone-text {
    color: var(--gray-600);
    font-size: 13px;
}

/* Role Badge */
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

    .pagination .page-item:first-child .page-link::before {
        content: '←';
    }

    .pagination .page-item:last-child .page-link::before {
        content: '→';
    }
}
</style>

@endsection