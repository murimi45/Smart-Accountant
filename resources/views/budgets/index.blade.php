@extends('layouts.app')

@section('main')
<div class="main-wrapper">
    {{-- Page Header --}}
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h4 class="mb-1">Expense Budgets</h4>
                <p class="text-muted mb-0">Set spending limits per category for each term</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="{{ route('reports.budget-variance', ['term_id' => $termId]) }}" class="btn btn-outline-secondary">
                    <i class="fa fa-chart-bar me-2"></i>Variance Report
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

    {{-- Term Filter Card --}}
    <div class="card filter-card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('budgets.index') }}" class="filter-form">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label"><i class="fa fa-calendar me-1"></i>Term</label>
                        <select name="term_id" class="form-control" onchange="this.form.submit()">
                            @foreach($terms as $term)
                                <option value="{{ $term->id }}" {{ (int) $termId === (int) $term->id ? 'selected' : '' }}>
                                    {{ $term->name }} - {{ $term->year }}
                                    @if($currentTerm && $term->id === $currentTerm->id) (current) @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Budget Table Card --}}
    @if($selectedTerm)
        <form method="POST" action="{{ route('budgets.update') }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="term_id" value="{{ $termId }}">

            <div class="card table-card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fa fa-wallet me-2"></i>Budget for {{ $selectedTerm->name }} {{ $selectedTerm->year }}
                        </h5>
                        <button type="submit" class="btn btn-success">
                            <i class="fa fa-save me-2"></i>Save Budgets
                        </button>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table admin-table mb-0">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th style="width: 180px;">Budget (KSh)</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categories as $category)
                                    @php $budget = $budgets->get($category->id); @endphp
                                    <tr>
                                        <td>
                                            <span class="category-name">{{ $category->name }}</span>
                                        </td>
                                        <td>
                                            <input type="number"
                                                   name="amounts[{{ $category->id }}]"
                                                   class="form-control form-control-sm"
                                                   min="0"
                                                   step="0.01"
                                                   value="{{ old('amounts.'.$category->id, $budget?->amount) }}"
                                                   placeholder="0.00">
                                        </td>
                                        <td>
                                            <input type="text"
                                                   name="notes[{{ $category->id }}]"
                                                   class="form-control form-control-sm"
                                                   value="{{ old('notes.'.$category->id, $budget?->notes) }}"
                                                   placeholder="Optional note">
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-5">
                                            <div class="empty-state">
                                                <i class="fa fa-inbox fa-3x mb-3"></i>
                                                <p class="mb-2">No expense categories yet</p>
                                                <small class="d-block mb-3">Add categories before setting budgets</small>
                                                <a href="{{ route('expense_categories.index') }}" class="btn btn-success">
                                                    <i class="fa fa-plus me-2"></i>Add Categories
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
        </form>
    @else
        <div class="alert alert-warning">
            <i class="fa fa-exclamation-triangle me-2"></i>Select a term to set budgets.
        </div>
    @endif
</div>

<style>
/* Base Variables — matched to Admin Management page */
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

.form-control-sm {
    padding: 6px 10px;
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
    color: #fff;
}

.btn-success:hover {
    background-color: var(--success-dark);
    border-color: var(--success-dark);
    color: #fff;
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

.category-name {
    font-weight: 500;
    color: var(--gray-900);
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