@extends('layouts.app')
@section('main')

<div class="main-wrapper">
    {{-- Page Header --}}
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h4 class="mb-1">Expense Management</h4>
                <p class="text-muted mb-0">Track and manage all business expenses</p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <a href="{{ route('expenses.create') }}" class="btn btn-success">
                    <i class="fa fa-plus me-2"></i>Add Expense
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

    {{-- Filters Card --}}
    <div class="card filter-card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('expenses.index') }}" class="filter-form">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label"><i class="fa fa-tag me-1"></i>Category</label>
                        <select name="category_id" class="form-select">
                            <option value="">All Categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label"><i class="fa fa-credit-card me-1"></i>Payment Method</label>
                        <select name="payment_method" class="form-select">
                            <option value="">All Methods</option>
                            <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="mpesa" {{ request('payment_method') == 'mpesa' ? 'selected' : '' }}>M-Pesa</option>
                            <option value="bank" {{ request('payment_method') == 'bank' ? 'selected' : '' }}>Bank Transfer</option>
                            <option value="cheque" {{ request('payment_method') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label"><i class="fa fa-search me-1"></i>Search Description</label>
                        <input type="text" 
                               name="description" 
                               class="form-control" 
                               value="{{ request('description') }}"
                               placeholder="Enter description keyword">
                    </div>

                    <div class="col-md-2">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="fa fa-filter me-1"></i>Filter
                            </button>
                            <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary" title="Reset">
                                <i class="fa fa-redo"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Expenses Table --}}
    <div class="card table-card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fa fa-list-alt me-2"></i>Expenses List
                </h5>
                <span class="badge bg-light text-dark">{{ $expenses->total() }} Total</span>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table expense-table mb-0">
                    <thead>
                        <tr>
                            <th style="width: 60px;">#</th>
                            <th>Category</th>
                            <th>Description</th>
                            <th>Amount</th>
                            <th>Payment Method</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $expense)
                        <tr>
                            <td>
                                <span class="row-number">{{ $loop->iteration }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="category-icon">
                                        {{ strtoupper(substr($expense->category?->name ?? 'N', 0, 1)) }}
                                    </div>
                                    <div class="ms-3">
                                        <div class="category-name">{{ $expense->category?->name ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($expense->description)
                                    <span class="description-text">{{ Str::limit($expense->description, 50) }}</span>
                                @else
                                    <span class="text-muted description-empty">
                                        <i class="fa fa-minus me-1"></i>No description
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span class="amount-text">KSh {{ number_format($expense->amount, 2) }}</span>
                            </td>
                            <td>
                                <span class="badge-method">{{ ucfirst($expense->payment_method) }}</span>
                            </td>
                            <td>
                                <span class="date-text">
                                    <i class="fa fa-calendar-day me-1"></i>
                                    {{ $expense->expense_date?->format('d M, Y') }}
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('expenses.edit', $expense->id) }}" 
                                       class="btn btn-sm btn-light" 
                                       title="Edit Expense">
                                        <i class="fa fa-edit"></i>
                                    </a>

                                    @if($expense->deleted_at)
                                        <form action="{{ route('expenses.restore', $expense->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button class="btn btn-sm btn-warning" 
                                                    title="Restore Expense">
                                                <i class="fa fa-undo"></i>
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('expenses.destroy', $expense->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger" 
                                                    onclick="return confirm('Are you sure you want to delete this expense?')"
                                                    title="Delete Expense">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fa fa-inbox fa-3x mb-3"></i>
                                    <p class="mb-0">No expenses found</p>
                                    <small>Try adjusting your filters or add a new expense</small>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        @if($expenses->hasPages())
        <div class="card-footer">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div class="mb-2 mb-sm-0">
                    <small class="text-muted">
                        Showing {{ $expenses->firstItem() }} to {{ $expenses->lastItem() }} of {{ $expenses->total() }} entries
                    </small>
                </div>
                <div>
                    {{ $expenses->links() }}
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

.btn-warning {
    background-color: var(--warning-color);
    border-color: var(--warning-color);
    color: white;
}

.btn-warning:hover {
    background-color: #d97706;
    border-color: #d97706;
    color: white;
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
.expense-table {
    font-size: 14px;
}

.expense-table thead {
    background-color: var(--gray-50);
    border-bottom: 2px solid var(--gray-200);
}

.expense-table thead th {
    font-weight: 600;
    color: var(--gray-700);
    padding: 12px 16px;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.expense-table tbody td {
    padding: 16px;
    vertical-align: middle;
    border-bottom: 1px solid var(--gray-100);
}

.expense-table tbody tr:hover {
    background-color: var(--gray-50);
}

/* Row Number */
.row-number {
    color: var(--gray-500);
    font-weight: 500;
    font-size: 13px;
}

/* Category */
.category-icon {
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
}

.category-name {
    font-weight: 500;
    color: var(--gray-900);
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

/* Amount */
.amount-text {
    font-weight: 600;
    color: var(--gray-900);
    font-size: 14px;
}

/* Payment Method Badge */
.badge-method {
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
    margin-top: 8px;
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

/* Pagination */
.pagination {
    margin: 0;
}

.pagination .page-link {
    border-radius: 6px;
    margin: 0 3px;
    border: 1px solid var(--gray-300);
    color: var(--gray-600);
    padding: 6px 12px;
    font-size: 14px;
}

.pagination .page-link:hover {
    background-color: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
}

.pagination .page-item.active .page-link {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
}

.pagination .page-item.disabled .page-link {
    color: var(--gray-400);
    background-color: var(--gray-50);
}

/* Responsive Design */
@media (max-width: 768px) {
    .category-icon {
        width: 32px;
        height: 32px;
        font-size: 14px;
    }
    
    .action-buttons .btn-sm {
        padding: 6px 10px;
        font-size: 12px;
    }
    
    .action-buttons .btn-sm i {
        margin: 0;
    }
    
    .expense-table {
        font-size: 13px;
    }

    .expense-table thead th,
    .expense-table tbody td {
        padding: 10px;
    }

    .description-text {
        font-size: 12px;
    }
}

/* Badge Styling Override */
.badge.bg-light {
    background-color: var(--gray-100) !important;
    color: var(--gray-700);
    padding: 4px 12px;
    font-weight: 500;
}
</style>

@endsection